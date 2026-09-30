import {
    AudioPresets,
    Room,
    RoomEvent,
    Track,
} from 'livekit-client';

class SmartMeetLiveKit {
    constructor() {
        this.room = null;
        this.connected = false;
    }

    async connect({ tokenUrl, csrfToken }) {
        if (this.room) {
            return this.room;
        }

        const response = await fetch(tokenUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error(`LiveKit token request failed (${response.status})`);
        }

        const data = await response.json();

        if (!data.server_url || !data.token) {
            throw new Error('Invalid LiveKit token response.');
        }

        const room = new Room({
            adaptiveStream: true,
            dynacast: true,
            audioCaptureDefaults: {
                autoGainControl: true,
                echoCancellation: true,
                noiseSuppression: true,
                channelCount: 1,
            },
            publishDefaults: {
                audioPreset: AudioPresets.speech,
                dtx: true,
                red: true,
                stopMicTrackOnMute: false,
            },
        });

        room.on(RoomEvent.Connected, () => {
            this.connected = true;
            window.dispatchEvent(new CustomEvent('smartmeet:livekit-connected'));
        });

        room.on(RoomEvent.Disconnected, () => {
            this.connected = false;
            window.dispatchEvent(new CustomEvent('smartmeet:livekit-disconnected'));
        });

        room.on(RoomEvent.Reconnecting, () => {
            window.dispatchEvent(new CustomEvent('smartmeet:livekit-reconnecting'));
        });

        room.on(RoomEvent.Reconnected, () => {
            this.connected = true;
            window.dispatchEvent(new CustomEvent('smartmeet:livekit-reconnected'));
        });

        room.on(RoomEvent.TrackSubscribed, (track, publication, participant) => {
            window.dispatchEvent(new CustomEvent('smartmeet:livekit-track-subscribed', {
                detail: {
                    track,
                    publication,
                    participant,
                },
            }));
        });

        room.on(RoomEvent.TrackUnsubscribed, (track, publication, participant) => {
            window.dispatchEvent(new CustomEvent('smartmeet:livekit-track-unsubscribed', {
                detail: {
                    track,
                    publication,
                    participant,
                },
            }));
        });

        room.on(RoomEvent.TrackStreamStateChanged, (publication, streamState, participant) => {
            window.dispatchEvent(new CustomEvent('smartmeet:livekit-track-stream-state-changed', {
                detail: {
                    publication,
                    streamState,
                    participant,
                },
            }));
        });

        room.on(RoomEvent.AudioPlaybackStatusChanged, () => {
            window.dispatchEvent(new CustomEvent('smartmeet:livekit-audio-playback-changed', {
                detail: {
                    canPlaybackAudio: Boolean(room.canPlaybackAudio),
                },
            }));
        });

        room.on(RoomEvent.ParticipantConnected, (participant) => {
            window.dispatchEvent(new CustomEvent('smartmeet:livekit-participant-connected', {
                detail: { participant },
            }));
        });

        room.on(RoomEvent.ParticipantDisconnected, (participant) => {
            window.dispatchEvent(new CustomEvent('smartmeet:livekit-participant-disconnected', {
                detail: { participant },
            }));
        });

        await room.connect(data.server_url, data.token);

        try {
            await room.startAudio();
        } catch (_) {}

        this.room = room;
        this.connected = true;

        return room;
    }

    async setMicrophoneEnabled(enabled) {
        if (!this.room) {
            throw new Error('LiveKit room is not connected.');
        }

        try {
            await this.room.startAudio();
        } catch (_) {}

        await this.room.localParticipant.setMicrophoneEnabled(
            Boolean(enabled),
            {
                autoGainControl: true,
                echoCancellation: true,
                noiseSuppression: true,
                channelCount: 1,
            }
        );
    }

    async setCameraEnabled(enabled) {
        if (!this.room) {
            throw new Error('LiveKit room is not connected.');
        }

        try {
            await this.room.startAudio();
        } catch (_) {}

        await this.room.localParticipant.setCameraEnabled(Boolean(enabled));
    }

    async setScreenShareEnabled(enabled) {
        if (!this.room) {
            throw new Error('LiveKit room is not connected.');
        }

        await this.room.localParticipant.setScreenShareEnabled(Boolean(enabled));
    }

    attachTrack(track, element) {
        if (!track || !element) {
            return;
        }

        if (track.kind === Track.Kind.Audio || track.kind === Track.Kind.Video) {
            track.attach(element);
        }
    }

    detachTrack(track) {
        if (!track) {
            return;
        }

        track.detach();
    }

    async disconnect() {
        if (!this.room) {
            return;
        }

        await this.room.disconnect();

        this.room = null;
        this.connected = false;
    }
}

window.SmartMeetLiveKit = new SmartMeetLiveKit();
