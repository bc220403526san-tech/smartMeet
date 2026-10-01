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

        // LiveKit is the single owner of meeting media. Keep this ownership
        // flag true even while LiveKit is temporarily reconnecting so the
        // legacy mesh-P2P layer can never take over during a short outage.
        this.mediaMode = 'livekit';
    }

    isMediaOwner() {
        return this.mediaMode === 'livekit';
    }

    normalizeServerUrl(serverUrl) {
        let value = String(serverUrl || '').trim();

        if (!value) {
            throw new Error('LiveKit server URL is missing.');
        }

        // The LiveKit JS SDK expects the base WebSocket server URL, for example
        // wss://livekit.smartmeet.live. It builds the /rtc connection endpoint
        // itself. Older backend values may contain /rtc or /rtc/v1 already;
        // remove those suffixes to prevent malformed URLs such as
        // /rtc/v1/access_token=....
        if (!/^wss?:\/\//i.test(value)) {
            value = `wss://${value}`;
        }

        try {
            const url = new URL(value);

            if (url.protocol === 'http:') url.protocol = 'ws:';
            if (url.protocol === 'https:') url.protocol = 'wss:';

            const path = url.pathname.replace(/\/+$/, '');

            if (path === '/rtc' || path === '/rtc/v1') {
                url.pathname = '';
            }

            url.search = '';
            url.hash = '';

            return url.toString().replace(/\/$/, '');
        } catch (error) {
            throw new Error(`Invalid LiveKit server URL: ${value}`);
        }
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

        const serverUrl = this.normalizeServerUrl(data.server_url);

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
            // Do NOT change mediaMode here. A transient LiveKit disconnect or
            // reconnect window must never activate legacy mesh-P2P media.
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
                detail: { track, publication, participant },
            }));
        });

        room.on(RoomEvent.TrackUnsubscribed, (track, publication, participant) => {
            window.dispatchEvent(new CustomEvent('smartmeet:livekit-track-unsubscribed', {
                detail: { track, publication, participant },
            }));
        });

        room.on(RoomEvent.TrackStreamStateChanged, (publication, streamState, participant) => {
            window.dispatchEvent(new CustomEvent('smartmeet:livekit-track-stream-state-changed', {
                detail: { publication, streamState, participant },
            }));
        });

        room.on(RoomEvent.AudioPlaybackStatusChanged, () => {
            window.dispatchEvent(new CustomEvent('smartmeet:livekit-audio-playback-changed', {
                detail: { canPlaybackAudio: Boolean(room.canPlaybackAudio) },
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

        try {
            await room.connect(serverUrl, data.token, {
                autoSubscribe: true,
            });
        } catch (error) {
            try {
                await room.disconnect();
            } catch (disconnectError) {}

            throw error;
        }

        try {
            await room.startAudio();
        } catch (error) {}

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
        } catch (error) {}

        return this.room.localParticipant.setMicrophoneEnabled(
            Boolean(enabled),
            {
                autoGainControl: true,
                echoCancellation: true,
                noiseSuppression: true,
                channelCount: 1,
            },
            {
                audioPreset: AudioPresets.speech,
                dtx: true,
                red: true,
                source: Track.Source.Microphone,
                stopMicTrackOnMute: false,
            },
        );
    }

    async setCameraEnabled(enabled) {
        if (!this.room) {
            throw new Error('LiveKit room is not connected.');
        }

        try {
            await this.room.startAudio();
        } catch (error) {
            // Audio playback unlock must never block camera publishing.
        }

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
        // Keep mediaMode='livekit'. disconnect() is only used during page
        // cleanup; it must not hand media ownership back to the mesh layer.
    }
}

window.SmartMeetLiveKit = new SmartMeetLiveKit();
