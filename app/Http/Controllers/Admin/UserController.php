<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingParticipantLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('role')) $query->where('role', $request->role);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%{$search}%")->orWhere('email','like',"%{$search}%")->orWhere('role','like',"%{$search}%"));
        }
        $users=$query->latest()->paginate(6)->withQueryString();
        $totalUsers=User::count(); $activeUsers=User::where('is_active',1)->count(); $inactiveUsers=User::where('is_active',0)->count();
        return view('admin.users.index', compact('users','totalUsers','activeUsers','inactiveUsers'));
    }
    public function create(){ return view('admin.users.create'); }
    public function store(Request $request)
    {
        $request->validate(['name'=>'required|string|max:255','email'=>['required','email:rfc','regex:/^.+@.+\..+$/','unique:users,email'],'password'=>'required|min:6','role'=>'required|in:admin,organizer,participant','image'=>'nullable|mimes:jpg,jpeg,png,webp,avif|max:2048']);
        $imagePath=$request->hasFile('image')?$request->file('image')->store('user_images','public'):null;
        User::create(['name'=>$request->name,'email'=>$request->email,'password'=>Hash::make($request->password),'role'=>$request->role,'is_active'=>$request->boolean('is_active'),'image'=>$imagePath]);
        return redirect()->route('admin.users.index')->with('success','User created successfully!');
    }
    private function setImageUrl(User $user): void
    {
        $user->image_url=$user->image?(str_starts_with($user->image,'http')?$user->image:Storage::url($user->image)):asset('images/default-avatar.png');
    }
    public function show(User $user)
    {
        $this->setImageUrl($user);
        $meetingCount=match($user->role){
            'participant'=>MeetingParticipantLog::where('user_id',$user->id)->distinct()->count('meeting_id'),
            'organizer'=>Meeting::where('organizer_id',$user->id)->count(),
            default=>0,
        };
        return view('admin.users.show',compact('user','meetingCount'));
    }
    public function meetingHistory(User $user)
    {
        abort_unless(in_array($user->role,['participant','organizer'],true),404);
        $this->setImageUrl($user);

        if($user->role==='organizer'){
            $meetings=Meeting::query()->where('organizer_id',$user->id)->withCount('participants')->orderByDesc('date')->orderByDesc('time')->get()->map(function($meeting){
                $seconds=($meeting->organizer_joined_at && $meeting->organizer_left_at)?(int)$meeting->organizer_joined_at->diffInSeconds($meeting->organizer_left_at):0;
                return (object)['meeting'=>$meeting,'first_joined_at'=>$meeting->organizer_joined_at,'last_left_at'=>$meeting->organizer_left_at,'total_seconds'=>$seconds,'sessions'=>collect(),'participants_count'=>$meeting->participants_count];
            });
        } else {
            $logs=MeetingParticipantLog::with(['meeting.organizer'])->where('user_id',$user->id)->orderByDesc('joined_at')->get();
            $meetings=$logs->groupBy('meeting_id')->map(function($sessions){
                $ordered=$sessions->sortBy('joined_at')->values(); $first=$ordered->first();
                $last=$sessions->sortByDesc(fn($s)=>$s->left_at?->timestamp ?? $s->joined_at?->timestamp ?? 0)->first();
                $seconds=(int)$sessions->sum(fn($s)=>(!$s->joined_at || !$s->left_at)?0:(int)$s->joined_at->diffInSeconds($s->left_at));
                return (object)['meeting'=>$first?->meeting,'first_joined_at'=>$first?->joined_at,'last_left_at'=>$last?->left_at,'total_seconds'=>$seconds,'sessions'=>$ordered,'participants_count'=>null];
            })->filter(fn($item)=>$item->meeting)->values();
        }
        return view('admin.users.meeting-history',compact('user','meetings'));
    }
    public function edit(User $user){ return view('admin.users.edit',compact('user')); }
    public function update(Request $request,User $user)
    {
        $request->validate(['name'=>'required|string|max:255','email'=>['required','email:rfc','regex:/^.+@.+\..+$/','unique:users,email,'.$user->id],'role'=>'required|in:admin,organizer,participant','image'=>'nullable|mimes:jpg,jpeg,png,webp,avif|max:2048']);
        $data=['name'=>$request->name,'email'=>$request->email,'role'=>$request->role,'is_active'=>$request->boolean('is_active')];
        if($request->remove_image){ if($user->image&&!str_starts_with($user->image,'http'))Storage::disk('public')->delete($user->image); $data['image']=null; }
        if($request->hasFile('image')){ if($user->image&&!str_starts_with($user->image,'http'))Storage::disk('public')->delete($user->image); $data['image']=$request->file('image')->store('user_images','public'); }
        $user->update($data); return back()->with('success','User updated successfully!');
    }
    public function destroy(User $user)
    {
        if($user->id===auth()->id())return back()->with('error','You cannot delete your own account!');
        if($user->image&&!str_starts_with($user->image,'http'))Storage::disk('public')->delete($user->image);
        $user->delete(); return redirect()->route('admin.users.index')->with('success','User removed successfully!');
    }
    public function toggleStatus(User $user)
    {
        if($user->id===auth()->id())return back()->with('error','You cannot deactivate your own account!');
        $newStatus=!$user->is_active; $user->update(['is_active'=>$newStatus]);
        Notification::create(['user_id'=>$user->id,'title'=>$newStatus?'Account Activated':'Account Deactivated','message'=>$newStatus?'Your account has been activated by an administrator. You now have full access again.':'Your account has been deactivated by an administrator.','link'=>null]);
        return back()->with('success','User status updated.');
    }
    public function changeRole(Request $request,User $user)
    {
        if($user->id===auth()->id())return back()->with('error','You cannot change your own role!');
        $request->validate(['role'=>'required|in:admin,organizer,participant']); $oldRole=$user->role; $newRole=$request->role;
        if($oldRole===$newRole)return back()->with('success','No change — user already has this role.');
        $user->update(['role'=>$newRole]);
        Notification::create(['user_id'=>$user->id,'title'=>'Your Role Has Been Updated','message'=>'Your role has been changed from '.ucfirst($oldRole).' to '.ucfirst($newRole).'.','link'=>null]);
        return back()->with('success','User role changed to '.ucfirst($newRole).' successfully!');
    }
}
