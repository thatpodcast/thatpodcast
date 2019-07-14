const $ = jQuery = require('jquery');
require('jplayer');
(function ($) {
    $(document).ready(function($) {
        const $player = $("#jquery_jplayer_audio_1");
        $player.jPlayer({
            swfPath: "/jplayer/jquery.jplayer.swf",
            ready: function(event) {
                $(this).jPlayer("setMedia", {
                    mp3: $player.data('media-url')
                });
            },
            play: function() { // Avoid multiple jPlayers playing together.
                console.log('PLAY!');
                $(this).jPlayer("pauseOthers");
            },
            timeFormat: {
                padMin: false
            },
            preload: "none",
            supplied: "mp3",
            cssSelectorAncestor: "#jp_container_audio_1",
            smoothPlayBar: true,
            remainingDuration: true,
            keyEnabled: false,
            keyBindings: {
                // Disable some of the default key controls
                muted: null,
                volumeUp: null,
                volumeDown: null
            },
            wmode: "window"
        });
    });
})(jQuery);
