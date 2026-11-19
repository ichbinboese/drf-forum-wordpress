<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

use Flarum\Extend;
use Flarum\Frontend\Document;

return [
    (new Extend\Frontend('forum'))
        ->content(function (Document $document) {
            $topbar = '<div class="drf-flarum-topbar">'
                . '<div class="drf-topbar-left">'
                . '<a href="/" class="drf-topbar-brand"><span>d.r.f</span> <span class="badge">Augenblicke</span></a>'
                . '</div>'
                . '<ul class="drf-topbar-nav">'
                . '<li><a href="/">Hauptseite</a></li>'
                . '<li><a href="/verein/">Verein</a></li>'
                . '<li><a href="/augenblicke/">Treffen 2026/2027</a></li>'
                . '<li><a href="/tagungsbuero/">Tagungsb&uuml;ro &amp; Anmeldung</a></li>'
                . '<li><a href="/sozialgremium/">Sozialgremium</a></li>'
                . '<li><a href="/shuttleservice/">Shuttleservice</a></li>'
                . '<li><a href="/workshops/">Workshops</a></li>'
                . '<li><a href="/forum/" class="active">Community Forum</a></li>'
                . '</ul>'
                . '</div>';

            $document->foot[] = "<script>
            (function() {
                function injectBar() {
                    var header = document.querySelector('.App-header');
                    if (header && !document.querySelector('.drf-flarum-topbar')) {
                        var div = document.createElement('div');
                        div.innerHTML = " . json_encode($topbar) . ";
                        header.parentNode.insertBefore(div.firstChild, header);
                    }
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', injectBar);
                } else {
                    injectBar();
                }
                setTimeout(injectBar, 500);
            })();
            </script>";
        }),
];
