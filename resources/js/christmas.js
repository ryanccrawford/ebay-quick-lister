(function(g, k) {
    function U(U, ka) {
        function V(b) {
            return (
                c.preferFlash &&
                v &&
                !c.ignoreFlash &&
                c.flash[b] !== k &&
                c.flash[b]
            );
        }

        function q(b) {
            return function(c) {
                var d = this._s;
                return !d || !d._a ? null : b.call(this, c);
            };
        }
        this.setupOptions = {
            url: U || null,
            flashVersion: 8,
            debugMode: !0,
            debugFlash: !1,
            useConsole: !0,
            consoleOnly: !0,
            waitForWindowLoad: !1,
            bgColor: "#ffffff",
            useHighPerformance: !1,
            flashPollingInterval: null,
            html5PollingInterval: null,
            flashLoadTimeout: 1e3,
            wmode: null,
            allowScriptAccess: "always",
            useFlashBlock: !1,
            useHTML5Audio: !0,
            html5Test: /^(probably|maybe)$/i,
            preferFlash: !1,
            noSWFCache: !1,
            idPrefix: "sound"
        };
        this.defaultOptions = {
            autoLoad: !1,
            autoPlay: !1,
            from: null,
            loops: 1,
            onid3: null,
            onload: null,
            whileloading: null,
            onplay: null,
            onpause: null,
            onresume: null,
            whileplaying: null,
            onposition: null,
            onstop: null,
            onfailure: null,
            onfinish: null,
            multiShot: !0,
            multiShotEvents: !1,
            position: null,
            pan: 0,
            stream: !0,
            to: null,
            type: null,
            usePolicyFile: !1,
            volume: 100
        };
        this.flash9Options = {
            isMovieStar: null,
            usePeakData: !1,
            useWaveformData: !1,
            useEQData: !1,
            onbufferchange: null,
            ondataerror: null
        };
        this.movieStarOptions = {
            bufferTime: 3,
            serverURL: null,
            onconnect: null,
            duration: null
        };
        this.audioFormats = {
            mp3: {
                type: [
                    'audio/mpeg; codecs\x3d"mp3"',
                    "audio/mpeg",
                    "audio/mp3",
                    "audio/MPA",
                    "audio/mpa-robust"
                ],
                required: !0
            },
            mp4: {
                related: ["aac", "m4a", "m4b"],
                type: [
                    'audio/mp4; codecs\x3d"mp4a.40.2"',
                    "audio/aac",
                    "audio/x-m4a",
                    "audio/MP4A-LATM",
                    "audio/mpeg4-generic"
                ],
                required: !1
            },
            ogg: { type: ["audio/ogg; codecs\x3dvorbis"], required: !1 },
            opus: {
                type: ["audio/ogg; codecs\x3dopus", "audio/opus"],
                required: !1
            },
            wav: {
                type: [
                    'audio/wav; codecs\x3d"1"',
                    "audio/wav",
                    "audio/wave",
                    "audio/x-wav"
                ],
                required: !1
            }
        };
        this.movieID = "sm2-container";
        this.id = ka || "sm2movie";
        this.debugID = "soundmanager-debug";
        this.debugURLParam = /([#?&])debug=1/i;
        this.versionNumber = "V2.97a.20130512+DEV";
        this.altURL = this.movieURL = this.version = null;
        this.enabled = this.swfLoaded = !1;
        this.oMC = null;
        this.sounds = {};
        this.soundIDs = [];
        this.didFlashBlock = this.muted = !1;
        this.filePattern = null;
        this.filePatterns = {
            flash8: /\.mp3(\?.*)?$/i,
            flash9: /\.mp3(\?.*)?$/i
        };
        this.features = {
            buffering: !1,
            peakData: !1,
            waveformData: !1,
            eqData: !1,
            movieStar: !1
        };
        this.sandbox = {};
        this.html5 = { usingFlash: null };
        this.flash = {};
        this.ignoreFlash = this.html5Only = !1;
        var Ja,
            c = this,
            Ka = null,
            l = null,
            W,
            s = navigator.userAgent,
            La = g.location.href.toString(),
            n = document,
            la,
            Ma,
            ma,
            m,
            x = [],
            K = !1,
            L = !1,
            p = !1,
            y = !1,
            na = !1,
            M,
            w,
            oa,
            X,
            pa,
            D,
            E,
            F,
            Na,
            qa,
            ra,
            Y,
            sa,
            Z,
            ta,
            G,
            ua,
            N,
            va,
            $,
            H,
            Oa,
            wa,
            Pa,
            xa,
            Qa,
            O = null,
            ya = null,
            P,
            za,
            I,
            aa,
            ba,
            r,
            Q = !1,
            Aa = !1,
            Ra,
            Sa,
            Ta,
            ca = 0,
            R = null,
            da,
            Ua = [],
            S,
            u = null,
            Va,
            ea,
            T,
            z,
            fa,
            Ba,
            Wa,
            t,
            fb = Array.prototype.slice,
            A = !1,
            Ca,
            v,
            Da,
            Xa,
            B,
            ga,
            Ya = 0,
            ha = s.match(/(ipad|iphone|ipod)/i),
            Za = s.match(/android/i),
            C = s.match(/msie/i),
            gb = s.match(/webkit/i),
            ia = s.match(/safari/i) && !s.match(/chrome/i),
            Ea = s.match(/opera/i);
        s.match(/firefox/i);
        var Fa = s.match(/(mobile|pre\/|xoom)/i) || ha || Za,
            $a = !La.match(/usehtml5audio/i) &&
            !La.match(/sm2\-ignorebadua/i) &&
            ia &&
            !s.match(/silk/i) &&
            s.match(/OS X 10_6_([3-7])/i),
            Ga = n.hasFocus !== k ? n.hasFocus() : null,
            ja = ia && (n.hasFocus === k || !n.hasFocus()),
            ab = !ja,
            bb = /(mp3|mp4|mpa|m4a|m4b)/i,
            Ha = n.location ? n.location.protocol.match(/http/i) : null,
            cb = !Ha ? "http://" : "",
            db = /^\s*audio\/(?:x-)?(?:mpeg4|aac|flv|mov|mp4||m4v|m4a|m4b|mp4v|3gp|3g2)\s*(?:$|;)/i,
            eb = "mpeg4 aac flv mov mp4 m4v f4v m4a m4b mp4v 3gp 3g2".split(
                " "
            ),
            hb = RegExp("\\.(" + eb.join("|") + ")(\\?.*)?$", "i");
        this.mimePattern = /^\s*audio\/(?:x-)?(?:mp(?:eg|3))\s*(?:$|;)/i;
        this.useAltURL = !Ha;
        var Ia;
        try {
            Ia =
                Audio !== k &&
                (Ea && opera !== k && 10 > opera.version() ?
                    new Audio(null) :
                    new Audio()
                ).canPlayType !== k;
        } catch (ib) {
            Ia = !1;
        }
        this.hasHTML5 = Ia;
        this.setup = function(b) {
            var e = !c.url;
            b !== k && p && u && c.ok();
            oa(b);
            b &&
                (e && N && b.url !== k && c.beginDelayedInit(), !N &&
                    b.url !== k && "complete" === n.readyState &&
                    setTimeout(G, 1));
            return c;
        };
        this.supported = this.ok = function() {
            return u ? p && !y : c.useHTML5Audio && c.hasHTML5;
        };
        this.getMovie = function(b) {
            return W(b) || n[b] || g[b];
        };
        this.createSound = function(b, e) {
            function d() {
                a = aa(a);
                c.sounds[a.id] = new Ja(a);
                c.soundIDs.push(a.id);
                return c.sounds[a.id];
            }
            var a,
                f = null;
            if (!p || !c.ok()) return !1;
            e !== k && (b = { id: b, url: e });
            a = w(b);
            a.url = da(a.url);
            void 0 === a.id && (a.id = c.setupOptions.idPrefix + Ya++);
            if (r(a.id, !0)) return c.sounds[a.id];
            if (ea(a))(f = d()), f._setup_html5(a);
            else {
                if (
                    c.html5Only ||
                    (c.html5.usingFlash && a.url && a.url.match(/data\:/i))
                )
                    return d();
                8 < m &&
                    null === a.isMovieStar &&
                    (a.isMovieStar = !(!a.serverURL &&
                        !(
                            (a.type && a.type.match(db)) ||
                            (a.url && a.url.match(hb))
                        )
                    ));
                a = ba(a, void 0);
                f = d();
                8 === m ?
                    l._createSound(a.id, a.loops || 1, a.usePolicyFile) :
                    (l._createSound(
                            a.id,
                            a.url,
                            a.usePeakData,
                            a.useWaveformData,
                            a.useEQData,
                            a.isMovieStar,
                            a.isMovieStar ? a.bufferTime : !1,
                            a.loops || 1,
                            a.serverURL,
                            a.duration || null,
                            a.autoPlay, !0,
                            a.autoLoad,
                            a.usePolicyFile
                        ),
                        a.serverURL ||
                        ((f.connected = !0),
                            a.onconnect && a.onconnect.apply(f)));
                !a.serverURL && (a.autoLoad || a.autoPlay) && f.load(a);
            }!a.serverURL && a.autoPlay && f.play();
            return f;
        };
        this.destroySound = function(b, e) {
            if (!r(b)) return !1;
            var d = c.sounds[b],
                a;
            d._iO = {};
            d.stop();
            d.unload();
            for (a = 0; a < c.soundIDs.length; a++)
                if (c.soundIDs[a] === b) {
                    c.soundIDs.splice(a, 1);
                    break;
                }
            e || d.destruct(!0);
            delete c.sounds[b];
            return !0;
        };
        this.load = function(b, e) {
            return !r(b) ? !1 : c.sounds[b].load(e);
        };
        this.unload = function(b) {
            return !r(b) ? !1 : c.sounds[b].unload();
        };
        this.onposition = this.onPosition = function(b, e, d, a) {
            return !r(b) ? !1 : c.sounds[b].onposition(e, d, a);
        };
        this.clearOnPosition = function(b, e, d) {
            return !r(b) ? !1 : c.sounds[b].clearOnPosition(e, d);
        };
        this.start = this.play = function(b, e) {
            var d = null,
                a = e && !(e instanceof Object);
            if (!p || !c.ok()) return !1;
            if (r(b, a)) a && (e = { url: e });
            else {
                if (!a) return !1;
                a && (e = { url: e });
                e && e.url && ((e.id = b), (d = c.createSound(e).play()));
            }
            null === d && (d = c.sounds[b].play(e));
            return d;
        };
        this.setPosition = function(b, e) {
            return !r(b) ? !1 : c.sounds[b].setPosition(e);
        };
        this.stop = function(b) {
            return !r(b) ? !1 : c.sounds[b].stop();
        };
        this.stopAll = function() {
            for (var b in c.sounds)
                c.sounds.hasOwnProperty(b) && c.sounds[b].stop();
        };
        this.pause = function(b) {
            return !r(b) ? !1 : c.sounds[b].pause();
        };
        this.pauseAll = function() {
            var b;
            for (b = c.soundIDs.length - 1; 0 <= b; b--)
                c.sounds[c.soundIDs[b]].pause();
        };
        this.resume = function(b) {
            return !r(b) ? !1 : c.sounds[b].resume();
        };
        this.resumeAll = function() {
            var b;
            for (b = c.soundIDs.length - 1; 0 <= b; b--)
                c.sounds[c.soundIDs[b]].resume();
        };
        this.togglePause = function(b) {
            return !r(b) ? !1 : c.sounds[b].togglePause();
        };
        this.setPan = function(b, e) {
            return !r(b) ? !1 : c.sounds[b].setPan(e);
        };
        this.setVolume = function(b, e) {
            return !r(b) ? !1 : c.sounds[b].setVolume(e);
        };
        this.mute = function(b) {
            var e = 0;
            b instanceof String && (b = null);
            if (b) return !r(b) ? !1 : c.sounds[b].mute();
            for (e = c.soundIDs.length - 1; 0 <= e; e--)
                c.sounds[c.soundIDs[e]].mute();
            return (c.muted = !0);
        };
        this.muteAll = function() {
            c.mute();
        };
        this.unmute = function(b) {
            b instanceof String && (b = null);
            if (b) return !r(b) ? !1 : c.sounds[b].unmute();
            for (b = c.soundIDs.length - 1; 0 <= b; b--)
                c.sounds[c.soundIDs[b]].unmute();
            c.muted = !1;
            return !0;
        };
        this.unmuteAll = function() {
            c.unmute();
        };
        this.toggleMute = function(b) {
            return !r(b) ? !1 : c.sounds[b].toggleMute();
        };
        this.getMemoryUse = function() {
            var b = 0;
            l && 8 !== m && (b = parseInt(l._getMemoryUse(), 10));
            return b;
        };
        this.disable = function(b) {
            var e;
            b === k && (b = !1);
            if (y) return !1;
            y = !0;
            for (e = c.soundIDs.length - 1; 0 <= e; e--)
                Pa(c.sounds[c.soundIDs[e]]);
            M(b);
            t.remove(g, "load", E);
            return !0;
        };
        this.canPlayMIME = function(b) {
            var e;
            c.hasHTML5 && (e = T({ type: b }));
            !e &&
                u &&
                (e =
                    b && c.ok() ?
                    !!((8 < m && b.match(db)) || b.match(c.mimePattern)) :
                    null);
            return e;
        };
        this.canPlayURL = function(b) {
            var e;
            c.hasHTML5 && (e = T({ url: b }));
            !e && u && (e = b && c.ok() ? !!b.match(c.filePattern) : null);
            return e;
        };
        this.canPlayLink = function(b) {
            return b.type !== k && b.type && c.canPlayMIME(b.type) ?
                !0 :
                c.canPlayURL(b.href);
        };
        this.getSoundById = function(b, e) {
            return !b ? null : c.sounds[b];
        };
        this.onready = function(b, c) {
            if ("function" === typeof b) c || (c = g), pa("onready", b, c), D();
            else throw P("needFunction", "onready");
            return !0;
        };
        this.ontimeout = function(b, c) {
            if ("function" === typeof b)
                c || (c = g), pa("ontimeout", b, c), D({ type: "ontimeout" });
            else throw P("needFunction", "ontimeout");
            return !0;
        };
        this._wD = this._writeDebug = function(b, c) {
            return !0;
        };
        this._debug = function() {};
        this.reboot = function(b, e) {
            var d, a, f;
            for (d = c.soundIDs.length - 1; 0 <= d; d--)
                c.sounds[c.soundIDs[d]].destruct();
            if (l)
                try {
                    C && (ya = l.innerHTML), (O = l.parentNode.removeChild(l));
                } catch (k) {}
            ya = O = u = l = null;
            c.enabled = N = p = Q = Aa = K = L = y = A = c.swfLoaded = !1;
            c.soundIDs = [];
            c.sounds = {};
            Ya = 0;
            if (b) x = [];
            else
                for (d in x)
                    if (x.hasOwnProperty(d)) {
                        a = 0;
                        for (f = x[d].length; a < f; a++) x[d][a].fired = !1;
                    }
            c.html5 = { usingFlash: null };
            c.flash = {};
            c.html5Only = !1;
            c.ignoreFlash = !1;
            g.setTimeout(function() {
                ta();
                e || c.beginDelayedInit();
            }, 20);
            return c;
        };
        this.reset = function() {
            return c.reboot(!0, !0);
        };
        this.getMoviePercent = function() {
            return l && "PercentLoaded" in l ? l.PercentLoaded() : null;
        };
        this.beginDelayedInit = function() {
            na = !0;
            G();
            setTimeout(function() {
                if (Aa) return !1;
                $();
                Z();
                return (Aa = !0);
            }, 20);
            F();
        };
        this.destruct = function() {
            c.disable(!0);
        };
        Ja = function(b) {
            var e,
                d,
                a = this,
                f,
                h,
                J,
                g,
                n,
                q,
                s = !1,
                p = [],
                u = 0,
                x,
                y,
                v = null,
                z;
            d = e = null;
            this.sID = this.id = b.id;
            this.url = b.url;
            this._iO = this.instanceOptions = this.options = w(b);
            this.pan = this.options.pan;
            this.volume = this.options.volume;
            this.isHTML5 = !1;
            this._a = null;
            z = this.url ? !1 : !0;
            this.id3 = {};
            this._debug = function() {};
            this.load = function(b) {
                var e = null,
                    d;
                b !== k ?
                    (a._iO = w(b, a.options)) :
                    ((b = a.options),
                        (a._iO = b),
                        v &&
                        v !== a.url &&
                        ((a._iO.url = a.url), (a.url = null)));
                a._iO.url || (a._iO.url = a.url);
                a._iO.url = da(a._iO.url);
                d = a.instanceOptions = a._iO;
                if (!d.url && !a.url) return a;
                if (d.url === a.url && 0 !== a.readyState && 2 !== a.readyState)
                    return (
                        3 === a.readyState &&
                        d.onload &&
                        ga(a, function() {
                            d.onload.apply(a, [!!a.duration]);
                        }),
                        a
                    );
                a.loaded = !1;
                a.readyState = 1;
                a.playState = 0;
                a.id3 = {};
                if (ea(d))
                    (e = a._setup_html5(d)),
                    e._called_load ||
                    ((a._html5_canplay = !1),
                        a.url !== d.url &&
                        ((a._a.src = d.url), a.setPosition(0)),
                        (a._a.autobuffer = "auto"),
                        (a._a.preload = "auto"),
                        (a._a._called_load = !0));
                else {
                    if (
                        c.html5Only ||
                        (a._iO.url && a._iO.url.match(/data\:/i))
                    )
                        return a;
                    try {
                        (a.isHTML5 = !1),
                        (a._iO = ba(aa(d))),
                        (d = a._iO),
                        8 === m ?
                            l._load(
                                a.id,
                                d.url,
                                d.stream,
                                d.autoPlay,
                                d.usePolicyFile
                            ) :
                            l._load(
                                a.id,
                                d.url, !!d.stream, !!d.autoPlay,
                                d.loops || 1, !!d.autoLoad,
                                d.usePolicyFile
                            );
                    } catch (f) {
                        H({ type: "SMSOUND_LOAD_JS_EXCEPTION", fatal: !0 });
                    }
                }
                a.url = d.url;
                return a;
            };
            this.unload = function() {
                0 !== a.readyState &&
                    (a.isHTML5 ?
                        (g(), a._a && (a._a.pause(), (v = fa(a._a)))) :
                        8 === m ?
                        l._unload(a.id, "about:blank") :
                        l._unload(a.id),
                        f());
                return a;
            };
            this.destruct = function(b) {
                a.isHTML5 ?
                    (g(),
                        a._a &&
                        (a._a.pause(),
                            fa(a._a),
                            A || J(),
                            (a._a._s = null),
                            (a._a = null))) :
                    ((a._iO.onfailure = null), l._destroySound(a.id));
                b || c.destroySound(a.id, !0);
            };
            this.start = this.play = function(b, e) {
                var d, f, h, g, J;
                f = !0;
                f = null;
                e = e === k ? !0 : e;
                b || (b = {});
                a.url && (a._iO.url = a.url);
                a._iO = w(a._iO, a.options);
                a._iO = w(b, a._iO);
                a._iO.url = da(a._iO.url);
                a.instanceOptions = a._iO;
                if (!a.isHTML5 && a._iO.serverURL && !a.connected)
                    return a.getAutoPlay() || a.setAutoPlay(!0), a;
                ea(a._iO) && (a._setup_html5(a._iO), n());
                1 === a.playState &&
                    !a.paused &&
                    ((d = a._iO.multiShot),
                        d || (a.isHTML5 && a.setPosition(a._iO.position), (f = a)));
                if (null !== f) return f;
                b.url &&
                    b.url !== a.url &&
                    (!a.readyState && !a.isHTML5 && 8 === m && z ?
                        (z = !1) :
                        a.load(a._iO));
                a.loaded ||
                    (0 === a.readyState ?
                        (!a.isHTML5 && !c.html5Only ?
                            ((a._iO.autoPlay = !0), a.load(a._iO)) :
                            a.isHTML5 ?
                            a.load(a._iO) :
                            (f = a),
                            (a.instanceOptions = a._iO)) :
                        2 === a.readyState && (f = a));
                if (null !== f) return f;
                !a.isHTML5 &&
                    9 === m && 0 < a.position && a.position === a.duration &&
                    (b.position = 0);
                if (
                    a.paused &&
                    0 <= a.position &&
                    (!a._iO.serverURL || 0 < a.position)
                )
                    a.resume();
                else {
                    a._iO = w(b, a._iO);
                    if (
                        null !== a._iO.from &&
                        null !== a._iO.to &&
                        0 === a.instanceCount &&
                        0 === a.playState &&
                        !a._iO.serverURL
                    ) {
                        d = function() {
                            a._iO = w(b, a._iO);
                            a.play(a._iO);
                        };
                        if (a.isHTML5 && !a._html5_canplay)
                            a.load({ _oncanplay: d }), (f = !1);
                        else if (!a.isHTML5 &&
                            !a.loaded &&
                            (!a.readyState || 2 !== a.readyState)
                        )
                            a.load({ onload: d }), (f = !1);
                        if (null !== f) return f;
                        a._iO = y();
                    }
                    (!a.instanceCount ||
                        a._iO.multiShotEvents ||
                        (a.isHTML5 && a._iO.multiShot && !A) ||
                        (!a.isHTML5 && 8 < m && !a.getAutoPlay())) &&
                    a.instanceCount++;
                    a._iO.onposition && 0 === a.playState && q(a);
                    a.playState = 1;
                    a.paused = !1;
                    a.position =
                        a._iO.position !== k && !isNaN(a._iO.position) ?
                        a._iO.position :
                        0;
                    a.isHTML5 || (a._iO = ba(aa(a._iO)));
                    a._iO.onplay && e && (a._iO.onplay.apply(a), (s = !0));
                    a.setVolume(a._iO.volume, !0);
                    a.setPan(a._iO.pan, !0);
                    a.isHTML5 ?
                        2 > a.instanceCount ?
                        (n(),
                            (f = a._setup_html5()),
                            a.setPosition(a._iO.position),
                            f.play()) :
                        ((h = new Audio(a._iO.url)),
                            (g = function() {
                                t.remove(h, "ended", g);
                                a._onfinish(a);
                                fa(h);
                                h = null;
                            }),
                            (J = function() {
                                t.remove(h, "canplay", J);
                                try {
                                    h.currentTime = a._iO.position / 1e3;
                                } catch (b) {}
                                h.play();
                            }),
                            t.add(h, "ended", g),
                            void 0 !== a._iO.volume &&
                            (h.volume = Math.max(
                                0,
                                Math.min(1, a._iO.volume / 100)
                            )),
                            a.muted && (h.muted = !0),
                            a._iO.position ?
                            t.add(h, "canplay", J) :
                            h.play()) :
                        ((f = l._start(
                                a.id,
                                a._iO.loops || 1,
                                9 === m ? a.position : a.position / 1e3,
                                a._iO.multiShot || !1
                            )),
                            9 === m &&
                            !f &&
                            a._iO.onplayerror &&
                            a._iO.onplayerror.apply(a));
                }
                return a;
            };
            this.stop = function(b) {
                var c = a._iO;
                1 === a.playState &&
                    (a._onbufferchange(0),
                        a._resetOnPosition(0),
                        (a.paused = !1),
                        a.isHTML5 || (a.playState = 0),
                        x(),
                        c.to && a.clearOnPosition(c.to),
                        a.isHTML5 ?
                        a._a &&
                        ((b = a.position),
                            a.setPosition(0),
                            (a.position = b),
                            a._a.pause(),
                            (a.playState = 0),
                            a._onTimer(),
                            g()) :
                        (l._stop(a.id, b), c.serverURL && a.unload()),
                        (a.instanceCount = 0),
                        (a._iO = {}),
                        c.onstop && c.onstop.apply(a));
                return a;
            };
            this.setAutoPlay = function(b) {
                a._iO.autoPlay = b;
                a.isHTML5 ||
                    (l._setAutoPlay(a.id, b),
                        b &&
                        !a.instanceCount &&
                        1 === a.readyState &&
                        a.instanceCount++);
            };
            this.getAutoPlay = function() {
                return a._iO.autoPlay;
            };
            this.setPosition = function(b) {
                b === k && (b = 0);
                var c = a.isHTML5 ?
                    Math.max(b, 0) :
                    Math.min(a.duration || a._iO.duration, Math.max(b, 0));
                a.position = c;
                b = a.position / 1e3;
                a._resetOnPosition(a.position);
                a._iO.position = c;
                if (a.isHTML5) {
                    if (a._a) {
                        if (a._html5_canplay) {
                            if (a._a.currentTime !== b)
                                try {
                                    (a._a.currentTime = b),
                                    (0 === a.playState || a.paused) &&
                                    a._a.pause();
                                } catch (e) {}
                        } else if (b) return a;
                        a.paused && a._onTimer(!0);
                    }
                } else
                    (b = 9 === m ? a.position : b),
                    a.readyState &&
                    2 !== a.readyState &&
                    l._setPosition(
                        a.id,
                        b,
                        a.paused || !a.playState,
                        a._iO.multiShot
                    );
                return a;
            };
            this.pause = function(b) {
                if (a.paused || (0 === a.playState && 1 !== a.readyState))
                    return a;
                a.paused = !0;
                a.isHTML5 ?
                    (a._setup_html5().pause(), g()) :
                    (b || b === k) && l._pause(a.id, a._iO.multiShot);
                a._iO.onpause && a._iO.onpause.apply(a);
                return a;
            };
            this.resume = function() {
                var b = a._iO;
                if (!a.paused) return a;
                a.paused = !1;
                a.playState = 1;
                a.isHTML5 ?
                    (a._setup_html5().play(), n()) :
                    (b.isMovieStar &&
                        !b.serverURL &&
                        a.setPosition(a.position),
                        l._pause(a.id, b.multiShot));
                !s && b.onplay ?
                    (b.onplay.apply(a), (s = !0)) :
                    b.onresume && b.onresume.apply(a);
                return a;
            };
            this.togglePause = function() {
                if (0 === a.playState)
                    return (
                        a.play({
                            position: 9 === m && !a.isHTML5 ?
                                a.position : a.position / 1e3
                        }),
                        a
                    );
                a.paused ? a.resume() : a.pause();
                return a;
            };
            this.setPan = function(b, c) {
                b === k && (b = 0);
                c === k && (c = !1);
                a.isHTML5 || l._setPan(a.id, b);
                a._iO.pan = b;
                c || ((a.pan = b), (a.options.pan = b));
                return a;
            };
            this.setVolume = function(b, e) {
                b === k && (b = 100);
                e === k && (e = !1);
                a.isHTML5 ?
                    a._a &&
                    (c.muted &&
                        !a.muted &&
                        ((a.muted = !0), (a._a.muted = !0)),
                        (a._a.volume = Math.max(0, Math.min(1, b / 100)))) :
                    l._setVolume(
                        a.id,
                        (c.muted && !a.muted) || a.muted ? 0 : b
                    );
                a._iO.volume = b;
                e || ((a.volume = b), (a.options.volume = b));
                return a;
            };
            this.mute = function() {
                a.muted = !0;
                a.isHTML5 ? a._a && (a._a.muted = !0) : l._setVolume(a.id, 0);
                return a;
            };
            this.unmute = function() {
                a.muted = !1;
                var b = a._iO.volume !== k;
                a.isHTML5 ?
                    a._a && (a._a.muted = !1) :
                    l._setVolume(a.id, b ? a._iO.volume : a.options.volume);
                return a;
            };
            this.toggleMute = function() {
                return a.muted ? a.unmute() : a.mute();
            };
            this.onposition = this.onPosition = function(b, c, e) {
                p.push({
                    position: parseInt(b, 10),
                    method: c,
                    scope: e !== k ? e : a,
                    fired: !1
                });
                return a;
            };
            this.clearOnPosition = function(a, b) {
                var c;
                a = parseInt(a, 10);
                if (isNaN(a)) return !1;
                for (c = 0; c < p.length; c++)
                    if (a === p[c].position && (!b || b === p[c].method))
                        p[c].fired && u--, p.splice(c, 1);
            };
            this._processOnPosition = function() {
                var b, c;
                b = p.length;
                if (!b || !a.playState || u >= b) return !1;
                for (b -= 1; 0 <= b; b--)
                    (c = p[b]), !c.fired &&
                    a.position >= c.position &&
                    ((c.fired = !0),
                        u++,
                        c.method.apply(c.scope, [c.position]));
                return !0;
            };
            this._resetOnPosition = function(a) {
                var b, c;
                b = p.length;
                if (!b) return !1;
                for (b -= 1; 0 <= b; b--)
                    (c = p[b]),
                    c.fired && a <= c.position && ((c.fired = !1), u--);
                return !0;
            };
            y = function() {
                var b = a._iO,
                    c = b.from,
                    e = b.to,
                    d,
                    f;
                f = function() {
                    a.clearOnPosition(e, f);
                    a.stop();
                };
                d = function() {
                    if (null !== e && !isNaN(e)) a.onPosition(e, f);
                };
                null !== c &&
                    !isNaN(c) &&
                    ((b.position = c), (b.multiShot = !1), d());
                return b;
            };
            q = function() {
                var b,
                    c = a._iO.onposition;
                if (c)
                    for (b in c)
                        if (c.hasOwnProperty(b))
                            a.onPosition(parseInt(b, 10), c[b]);
            };
            x = function() {
                var b,
                    c = a._iO.onposition;
                if (c)
                    for (b in c)
                        c.hasOwnProperty(b) &&
                        a.clearOnPosition(parseInt(b, 10));
            };
            n = function() {
                a.isHTML5 && Ra(a);
            };
            g = function() {
                a.isHTML5 && Sa(a);
            };
            f = function(b) {
                b || ((p = []), (u = 0));
                s = !1;
                a._hasTimer = null;
                a._a = null;
                a._html5_canplay = !1;
                a.bytesLoaded = null;
                a.bytesTotal = null;
                a.duration = a._iO && a._iO.duration ? a._iO.duration : null;
                a.durationEstimate = null;
                a.buffered = [];
                a.eqData = [];
                a.eqData.left = [];
                a.eqData.right = [];
                a.failures = 0;
                a.isBuffering = !1;
                a.instanceOptions = {};
                a.instanceCount = 0;
                a.loaded = !1;
                a.metadata = {};
                a.readyState = 0;
                a.muted = !1;
                a.paused = !1;
                a.peakData = { left: 0, right: 0 };
                a.waveformData = { left: [], right: [] };
                a.playState = 0;
                a.position = null;
                a.id3 = {};
            };
            f();
            this._onTimer = function(b) {
                var c,
                    f = !1,
                    h = {};
                if (a._hasTimer || b) {
                    if (
                        a._a &&
                        (b ||
                            ((0 < a.playState || 1 === a.readyState) &&
                                !a.paused))
                    )
                        (c = a._get_html5_duration()),
                        c !== e && ((e = c), (a.duration = c), (f = !0)),
                        (a.durationEstimate = a.duration),
                        (c = 1e3 * a._a.currentTime || 0),
                        c !== d && ((d = c), (f = !0)),
                        (f || b) && a._whileplaying(c, h, h, h, h);
                    return f;
                }
            };
            this._get_html5_duration = function() {
                var b = a._iO;
                return (b =
                        a._a && a._a.duration ?
                        1e3 * a._a.duration :
                        b && b.duration ?
                        b.duration :
                        null) &&
                    !isNaN(b) &&
                    Infinity !== b ?
                    b :
                    null;
            };
            this._apply_loop = function(a, b) {
                a.loop = 1 < b ? "loop" : "";
            };
            this._setup_html5 = function(b) {
                b = w(a._iO, b);
                var c = A ? Ka : a._a,
                    e = decodeURI(b.url),
                    d;
                A
                    ?
                    e === decodeURI(Ca) && (d = !0) :
                    e === decodeURI(v) && (d = !0);
                if (c) {
                    if (c._s)
                        if (A) c._s && c._s.playState && !d && c._s.stop();
                        else if (!A && e === decodeURI(v))
                        return a._apply_loop(c, b.loops), c;
                    d ||
                        (v && f(!1),
                            (c.src = b.url),
                            (Ca = v = a.url = b.url),
                            (c._called_load = !1));
                } else
                    b.autoLoad || b.autoPlay ?
                    ((a._a = new Audio(b.url)), a._a.load()) :
                    (a._a =
                        Ea && 10 > opera.version() ?
                        new Audio(null) :
                        new Audio()),
                    (c = a._a),
                    (c._called_load = !1),
                    A && (Ka = c);
                a.isHTML5 = !0;
                a._a = c;
                c._s = a;
                h();
                a._apply_loop(c, b.loops);
                b.autoLoad || b.autoPlay ?
                    a.load() :
                    ((c.autobuffer = !1), (c.preload = "auto"));
                return c;
            };
            h = function() {
                if (a._a._added_events) return !1;
                var b;
                a._a._added_events = !0;
                for (b in B)
                    B.hasOwnProperty(b) &&
                    a._a &&
                    a._a.addEventListener(b, B[b], !1);
                return !0;
            };
            J = function() {
                var b;
                a._a._added_events = !1;
                for (b in B)
                    B.hasOwnProperty(b) &&
                    a._a &&
                    a._a.removeEventListener(b, B[b], !1);
            };
            this._onload = function(b) {
                var c = !!b || (!a.isHTML5 && 8 === m && a.duration);
                a.loaded = c;
                a.readyState = c ? 3 : 2;
                a._onbufferchange(0);
                a._iO.onload &&
                    ga(a, function() {
                        a._iO.onload.apply(a, [c]);
                    });
                return !0;
            };
            this._onbufferchange = function(b) {
                if (
                    0 === a.playState ||
                    (b && a.isBuffering) ||
                    (!b && !a.isBuffering)
                )
                    return !1;
                a.isBuffering = 1 === b;
                a._iO.onbufferchange && a._iO.onbufferchange.apply(a);
                return !0;
            };
            this._onsuspend = function() {
                a._iO.onsuspend && a._iO.onsuspend.apply(a);
                return !0;
            };
            this._onfailure = function(b, c, e) {
                a.failures++;
                if (a._iO.onfailure && 1 === a.failures)
                    a._iO.onfailure(a, b, c, e);
            };
            this._onfinish = function() {
                var b = a._iO.onfinish;
                a._onbufferchange(0);
                a._resetOnPosition(0);
                a.instanceCount &&
                    (a.instanceCount--,
                        a.instanceCount ||
                        (x(),
                            (a.playState = 0),
                            (a.paused = !1),
                            (a.instanceCount = 0),
                            (a.instanceOptions = {}),
                            (a._iO = {}),
                            g(),
                            a.isHTML5 && (a.position = 0)),
                        (!a.instanceCount || a._iO.multiShotEvents) &&
                        b &&
                        ga(a, function() {
                            b.apply(a);
                        }));
            };
            this._whileloading = function(b, c, e, d) {
                var f = a._iO;
                a.bytesLoaded = b;
                a.bytesTotal = c;
                a.duration = Math.floor(e);
                a.bufferLength = d;
                a.durationEstimate = !a.isHTML5 && !f.isMovieStar ?
                    f.duration ?
                    a.duration > f.duration ?
                    a.duration :
                    f.duration :
                    parseInt(
                        (a.bytesTotal / a.bytesLoaded) * a.duration,
                        10
                    ) :
                    a.duration;
                a.isHTML5 || (a.buffered = [{ start: 0, end: a.duration }]);
                (3 !== a.readyState || a.isHTML5) &&
                f.whileloading &&
                    f.whileloading.apply(a);
            };
            this._whileplaying = function(b, c, e, d, f) {
                var h = a._iO;
                if (isNaN(b) || null === b) return !1;
                a.position = Math.max(0, b);
                a._processOnPosition();
                !a.isHTML5 &&
                    8 < m &&
                    (h.usePeakData &&
                        c !== k && c &&
                        (a.peakData = { left: c.leftPeak, right: c.rightPeak }),
                        h.useWaveformData &&
                        e !== k && e &&
                        (a.waveformData = {
                            left: e.split(","),
                            right: d.split(",")
                        }),
                        h.useEQData &&
                        f !== k && f && f.leftEQ &&
                        ((b = f.leftEQ.split(",")),
                            (a.eqData = b),
                            (a.eqData.left = b),
                            f.rightEQ !== k &&
                            f.rightEQ &&
                            (a.eqData.right = f.rightEQ.split(","))));
                1 === a.playState &&
                    (!a.isHTML5 &&
                        8 === m && !a.position && a.isBuffering &&
                        a._onbufferchange(0),
                        h.whileplaying && h.whileplaying.apply(a));
                return !0;
            };
            this._oncaptiondata = function(b) {
                a.captiondata = b;
                a._iO.oncaptiondata && a._iO.oncaptiondata.apply(a, [b]);
            };
            this._onmetadata = function(b, c) {
                var e = {},
                    d,
                    f;
                d = 0;
                for (f = b.length; d < f; d++) e[b[d]] = c[d];
                a.metadata = e;
                a._iO.onmetadata && a._iO.onmetadata.apply(a);
            };
            this._onid3 = function(b, c) {
                var e = [],
                    d,
                    f;
                d = 0;
                for (f = b.length; d < f; d++) e[b[d]] = c[d];
                a.id3 = w(a.id3, e);
                a._iO.onid3 && a._iO.onid3.apply(a);
            };
            this._onconnect = function(b) {
                b = 1 === b;
                if ((a.connected = b))
                    (a.failures = 0),
                    r(a.id) &&
                    (a.getAutoPlay() ?
                        a.play(k, a.getAutoPlay()) :
                        a._iO.autoLoad && a.load()),
                    a._iO.onconnect && a._iO.onconnect.apply(a, [b]);
            };
            this._ondataerror = function(b) {
                0 < a.playState &&
                    a._iO.ondataerror &&
                    a._iO.ondataerror.apply(a);
            };
        };
        va = function() {
            return n.body || n.getElementsByTagName("div")[0];
        };
        W = function(b) {
            return n.getElementById(b);
        };
        w = function(b, e) {
            var d = b || {},
                a,
                f;
            a = e === k ? c.defaultOptions : e;
            for (f in a)
                a.hasOwnProperty(f) &&
                d[f] === k &&
                (d[f] =
                    "object" !== typeof a[f] || null === a[f] ?
                    a[f] :
                    w(d[f], a[f]));
            return d;
        };
        ga = function(b, c) {
            !b.isHTML5 && 8 === m ? g.setTimeout(c, 0) : c();
        };
        X = {
            onready: 1,
            ontimeout: 1,
            defaultOptions: 1,
            flash9Options: 1,
            movieStarOptions: 1
        };
        oa = function(b, e) {
            var d,
                a = !0,
                f = e !== k,
                h = c.setupOptions;
            for (d in b)
                if (b.hasOwnProperty(d))
                    if (
                        "object" !== typeof b[d] ||
                        null === b[d] ||
                        b[d] instanceof Array ||
                        b[d] instanceof RegExp
                    )
                        f && X[e] !== k ?
                        (c[e][d] = b[d]) :
                        h[d] !== k ?
                        ((c.setupOptions[d] = b[d]), (c[d] = b[d])) :
                        X[d] === k ?
                        (a = !1) :
                        c[d] instanceof Function ?
                        c[d].apply(
                            c,
                            b[d] instanceof Array ? b[d] : [b[d]]
                        ) :
                        (c[d] = b[d]);
                    else if (X[d] === k) a = !1;
            else return oa(b[d], d);
            return a;
        };
        t = (function() {
            function b(a) {
                a = fb.call(a);
                var b = a.length;
                d
                    ?
                    ((a[1] = "on" + a[1]), 3 < b && a.pop()) :
                    3 === b && a.push(!1);
                return a;
            }

            function c(b, e) {
                var k = b.shift(),
                    g = [a[e]];
                if (d) k[g](b[0], b[1]);
                else k[g].apply(k, b);
            }
            var d = g.attachEvent,
                a = {
                    add: d ? "attachEvent" : "addEventListener",
                    remove: d ? "detachEvent" : "removeEventListener"
                };
            return {
                add: function() {
                    c(b(arguments), "add");
                },
                remove: function() {
                    c(b(arguments), "remove");
                }
            };
        })();
        B = {
            abort: q(function() {}),
            canplay: q(function() {
                var b = this._s,
                    c;
                if (b._html5_canplay) return !0;
                b._html5_canplay = !0;
                b._onbufferchange(0);
                c =
                    b._iO.position !== k && !isNaN(b._iO.position) ?
                    b._iO.position / 1e3 :
                    null;
                if (b.position && this.currentTime !== c)
                    try {
                        this.currentTime = c;
                    } catch (d) {}
                b._iO._oncanplay && b._iO._oncanplay();
            }),
            canplaythrough: q(function() {
                var b = this._s;
                b.loaded ||
                    (b._onbufferchange(0),
                        b._whileloading(
                            b.bytesLoaded,
                            b.bytesTotal,
                            b._get_html5_duration()
                        ),
                        b._onload(!0));
            }),
            ended: q(function() {
                this._s._onfinish();
            }),
            error: q(function() {
                this._s._onload(!1);
            }),
            loadeddata: q(function() {
                var b = this._s;
                !b._loaded && !ia && (b.duration = b._get_html5_duration());
            }),
            loadedmetadata: q(function() {}),
            loadstart: q(function() {
                this._s._onbufferchange(1);
            }),
            play: q(function() {
                this._s._onbufferchange(0);
            }),
            playing: q(function() {
                this._s._onbufferchange(0);
            }),
            progress: q(function(b) {
                var c = this._s,
                    d,
                    a,
                    f = 0,
                    f = b.target.buffered;
                d = b.loaded || 0;
                var h = b.total || 1;
                c.buffered = [];
                if (f && f.length) {
                    d = 0;
                    for (a = f.length; d < a; d++)
                        c.buffered.push({
                            start: 1e3 * f.start(d),
                            end: 1e3 * f.end(d)
                        });
                    f = 1e3 * (f.end(0) - f.start(0));
                    d = Math.min(1, f / (1e3 * b.target.duration));
                }
                isNaN(d) ||
                    (c._onbufferchange(0),
                        c._whileloading(d, h, c._get_html5_duration()),
                        d && h && d === h && B.canplaythrough.call(this, b));
            }),
            ratechange: q(function() {}),
            suspend: q(function(b) {
                var c = this._s;
                B.progress.call(this, b);
                c._onsuspend();
            }),
            stalled: q(function() {}),
            timeupdate: q(function() {
                this._s._onTimer();
            }),
            waiting: q(function() {
                this._s._onbufferchange(1);
            })
        };
        ea = function(b) {
            return !b || (!b.type && !b.url && !b.serverURL) ?
                !1 :
                b.serverURL || (b.type && V(b.type)) ?
                !1 :
                b.type ?
                T({ type: b.type }) :
                T({ url: b.url }) || c.html5Only || b.url.match(/data\:/i);
        };
        fa = function(b) {
            var e;
            b &&
                ((e = ia ?
                        "about:blank" :
                        c.html5.canPlayType("audio/wav") ?
                        "data:audio/wave;base64,/UklGRiYAAABXQVZFZm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YQIAAAD//w\x3d\x3d" :
                        "about:blank"),
                    (b.src = e),
                    void 0 !== b._called_unload && (b._called_load = !1));
            A && (Ca = null);
            return e;
        };
        T = function(b) {
            if (!c.useHTML5Audio || !c.hasHTML5) return !1;
            var e = b.url || null;
            b = b.type || null;
            var d = c.audioFormats,
                a;
            if (b && c.html5[b] !== k) return c.html5[b] && !V(b);
            if (!z) {
                z = [];
                for (a in d)
                    d.hasOwnProperty(a) &&
                    (z.push(a),
                        d[a].related && (z = z.concat(d[a].related)));
                z = RegExp("\\.(" + z.join("|") + ")(\\?.*)?$", "i");
            }
            a = e ? e.toLowerCase().match(z) : null;
            !a || !a.length ?
                b &&
                ((e = b.indexOf(";")),
                    (a = (-1 !== e ? b.substr(0, e) : b).substr(6))) :
                (a = a[1]);
            a && c.html5[a] !== k ?
                (e = c.html5[a] && !V(a)) :
                ((b = "audio/" + a),
                    (e = c.html5.canPlayType({ type: b })),
                    (e = (c.html5[a] = e) && c.html5[b] && !V(b)));
            return e;
        };
        Wa = function() {
            function b(a) {
                var b,
                    d = (b = !1);
                if (!e || "function" !== typeof e.canPlayType) return b;
                if (a instanceof Array) {
                    g = 0;
                    for (b = a.length; g < b; g++)
                        if (
                            c.html5[a[g]] ||
                            e.canPlayType(a[g]).match(c.html5Test)
                        )
                            (d = !0),
                            (c.html5[a[g]] = !0),
                            (c.flash[a[g]] = !!a[g].match(bb));
                    b = d;
                } else
                    (a =
                        e && "function" === typeof e.canPlayType ?
                        e.canPlayType(a) :
                        !1),
                    (b = !(!a || !a.match(c.html5Test)));
                return b;
            }
            if (!c.useHTML5Audio || !c.hasHTML5)
                return (u = c.html5.usingFlash = !0), !1;
            var e =
                Audio !== k ?
                Ea && 10 > opera.version() ?
                new Audio(null) :
                new Audio() :
                null,
                d,
                a,
                f = {},
                h,
                g;
            h = c.audioFormats;
            for (d in h)
                if (
                    h.hasOwnProperty(d) &&
                    ((a = "audio/" + d),
                        (f[d] = b(h[d].type)),
                        (f[a] = f[d]),
                        d.match(bb) ?
                        ((c.flash[d] = !0), (c.flash[a] = !0)) :
                        ((c.flash[d] = !1), (c.flash[a] = !1)),
                        h[d] && h[d].related)
                )
                    for (g = h[d].related.length - 1; 0 <= g; g--)
                        (f["audio/" + h[d].related[g]] = f[d]),
                        (c.html5[h[d].related[g]] = f[d]),
                        (c.flash[h[d].related[g]] = f[d]);
            f.canPlayType = e ? b : null;
            c.html5 = w(c.html5, f);
            c.html5.usingFlash = Va();
            u = c.html5.usingFlash;
            return !0;
        };
        sa = {};
        P = function() {};
        aa = function(b) {
            8 === m && 1 < b.loops && b.stream && (b.stream = !1);
            return b;
        };
        ba = function(b, c) {
            if (
                b &&
                !b.usePolicyFile &&
                (b.onid3 || b.usePeakData || b.useWaveformData || b.useEQData)
            )
                b.usePolicyFile = !0;
            return b;
        };
        la = function() {
            return !1;
        };
        Pa = function(b) {
            for (var c in b)
                b.hasOwnProperty(c) &&
                "function" === typeof b[c] &&
                (b[c] = la);
        };
        xa = function(b) {
            b === k && (b = !1);
            (y || b) && c.disable(b);
        };
        Qa = function(b) {
            var e = null;
            if (b)
                if (b.match(/\.swf(\?.*)?$/i)) {
                    if (
                        (e = b.substr(b.toLowerCase().lastIndexOf(".swf?") + 4))
                    )
                        return b;
                } else b.lastIndexOf("/") !== b.length - 1 && (b += "/");
            b =
                (b && -1 !== b.lastIndexOf("/") ?
                    b.substr(0, b.lastIndexOf("/") + 1) :
                    "./") + c.movieURL;
            c.noSWFCache && (b += "?ts\x3d" + new Date().getTime());
            return b;
        };
        ra = function() {
            m = parseInt(c.flashVersion, 10);
            8 !== m && 9 !== m && (c.flashVersion = m = 8);
            var b = c.debugMode || c.debugFlash ? "_debug.swf" : ".swf";
            c.useHTML5Audio &&
                !c.html5Only && c.audioFormats.mp4.required && 9 > m &&
                (c.flashVersion = m = 9);
            c.version =
                c.versionNumber +
                (c.html5Only ?
                    " (HTML5-only mode)" :
                    9 === m ?
                    " (AS3/Flash 9)" :
                    " (AS2/Flash 8)");
            8 < m ?
                ((c.defaultOptions = w(c.defaultOptions, c.flash9Options)),
                    (c.features.buffering = !0),
                    (c.defaultOptions = w(c.defaultOptions, c.movieStarOptions)),
                    (c.filePatterns.flash9 = RegExp(
                        "\\.(mp3|" + eb.join("|") + ")(\\?.*)?$",
                        "i"
                    )),
                    (c.features.movieStar = !0)) :
                (c.features.movieStar = !1);
            c.filePattern = c.filePatterns[8 !== m ? "flash9" : "flash8"];
            c.movieURL = (8 === m ?
                "soundmanager2.swf" :
                "soundmanager2_flash9.swf"
            ).replace(".swf", b);
            c.features.peakData = c.features.waveformData = c.features.eqData =
                8 < m;
        };
        Oa = function(b, c) {
            if (!l) return !1;
            l._setPolling(b, c);
        };
        wa = function() {};
        r = this.getSoundById;
        I = function() {
            var b = [];
            c.debugMode && b.push("sm2_debug");
            c.debugFlash && b.push("flash_debug");
            c.useHighPerformance && b.push("high_performance");
            return b.join(" ");
        };
        za = function() {
            P("fbHandler");
            var b = c.getMoviePercent(),
                e = { type: "FLASHBLOCK" };
            if (c.html5Only) return !1;
            c.ok() ?
                c.oMC &&
                (c.oMC.className = [
                    I(),
                    "movieContainer",
                    "swf_loaded" + (c.didFlashBlock ? " swf_unblocked" : "")
                ].join(" ")) :
                (u &&
                    (c.oMC.className =
                        I() +
                        " movieContainer " +
                        (null === b ? "swf_timedout" : "swf_error")),
                    (c.didFlashBlock = !0),
                    D({ type: "ontimeout", ignoreInit: !0, error: e }),
                    H(e));
        };
        pa = function(b, c, d) {
            x[b] === k && (x[b] = []);
            x[b].push({ method: c, scope: d || null, fired: !1 });
        };
        D = function(b) {
            b || (b = { type: c.ok() ? "onready" : "ontimeout" });
            if (
                (!p && b && !b.ignoreInit) ||
                ("ontimeout" === b.type && (c.ok() || (y && !b.ignoreInit)))
            )
                return !1;
            var e = { success: b && b.ignoreInit ? c.ok() : !y },
                d = b && b.type ? x[b.type] || [] : [],
                a = [],
                f,
                e = [e],
                h = u && !c.ok();
            b.error && (e[0].error = b.error);
            b = 0;
            for (f = d.length; b < f; b++) !0 !== d[b].fired && a.push(d[b]);
            if (a.length) {
                b = 0;
                for (f = a.length; b < f; b++)
                    a[b].scope ?
                    a[b].method.apply(a[b].scope, e) :
                    a[b].method.apply(this, e),
                    h || (a[b].fired = !0);
            }
            return !0;
        };
        E = function() {
            g.setTimeout(function() {
                c.useFlashBlock && za();
                D();
                "function" === typeof c.onload && c.onload.apply(g);
                c.waitForWindowLoad && t.add(g, "load", E);
            }, 1);
        };
        Da = function() {
            if (v !== k) return v;
            var b = !1,
                c = navigator,
                d = c.plugins,
                a,
                f = g.ActiveXObject;
            if (d && d.length)
                (c = c.mimeTypes) &&
                c["application/x-shockwave-flash"] &&
                c["application/x-shockwave-flash"].enabledPlugin &&
                c["application/x-shockwave-flash"].enabledPlugin
                .description &&
                (b = !0);
            else if (f !== k && !s.match(/MSAppHost/i)) {
                try {
                    a = new f("ShockwaveFlash.ShockwaveFlash");
                } catch (h) {
                    a = null;
                }
                b = !!a;
            }
            return (v = b);
        };
        Va = function() {
            var b,
                e,
                d = c.audioFormats;
            if (ha && s.match(/os (1|2|3_0|3_1)/i))
                (c.hasHTML5 = !1),
                (c.html5Only = !0),
                c.oMC && (c.oMC.style.display = "none");
            else if (c.useHTML5Audio && (!c.html5 || !c.html5.canPlayType))
                c.hasHTML5 = !1;
            if (c.useHTML5Audio && c.hasHTML5)
                for (e in ((S = !0), d))
                    if (d.hasOwnProperty(e) && d[e].required)
                        if (c.html5.canPlayType(d[e].type)) {
                            if (
                                c.preferFlash &&
                                (c.flash[e] || c.flash[d[e].type])
                            )
                                b = !0;
                        } else(S = !1), (b = !0);
            c.ignoreFlash && ((b = !1), (S = !0));
            c.html5Only = c.hasHTML5 && c.useHTML5Audio && !b;
            return !c.html5Only;
        };
        da = function(b) {
            var e,
                d,
                a = 0;
            if (b instanceof Array) {
                e = 0;
                for (d = b.length; e < d; e++)
                    if (b[e] instanceof Object) {
                        if (c.canPlayMIME(b[e].type)) {
                            a = e;
                            break;
                        }
                    } else if (c.canPlayURL(b[e])) {
                    a = e;
                    break;
                }
                b[a].url && (b[a] = b[a].url);
                b = b[a];
            }
            return b;
        };
        Ra = function(b) {
            b._hasTimer ||
                ((b._hasTimer = !0), !Fa &&
                    c.html5PollingInterval &&
                    (null === R &&
                        0 === ca &&
                        (R = setInterval(Ta, c.html5PollingInterval)),
                        ca++));
        };
        Sa = function(b) {
            b._hasTimer &&
                ((b._hasTimer = !1), !Fa && c.html5PollingInterval && ca--);
        };
        Ta = function() {
            var b;
            if (null !== R && !ca) return clearInterval(R), (R = null), !1;
            for (b = c.soundIDs.length - 1; 0 <= b; b--)
                c.sounds[c.soundIDs[b]].isHTML5 &&
                c.sounds[c.soundIDs[b]]._hasTimer &&
                c.sounds[c.soundIDs[b]]._onTimer();
        };
        H = function(b) {
            b = b !== k ? b : {};
            "function" === typeof c.onerror &&
                c.onerror.apply(g, [{ type: b.type !== k ? b.type : null }]);
            b.fatal !== k && b.fatal && c.disable();
        };
        Xa = function() {
            if (!$a || !Da()) return !1;
            var b = c.audioFormats,
                e,
                d;
            for (d in b)
                if (b.hasOwnProperty(d) && ("mp3" === d || "mp4" === d))
                    if (((c.html5[d] = !1), b[d] && b[d].related))
                        for (e = b[d].related.length - 1; 0 <= e; e--)
                            c.html5[b[d].related[e]] = !1;
        };
        this._setSandboxType = function(b) {};
        this._externalInterfaceOK = function(b) {
            if (c.swfLoaded) return !1;
            c.swfLoaded = !0;
            ja = !1;
            $a && Xa();
            setTimeout(ma, C ? 100 : 1);
        };
        $ = function(b, e) {
            function d(a, b) {
                return (
                    '\x3cparam name\x3d"' + a + '" value\x3d"' + b + '" /\x3e'
                );
            }
            if (K && L) return !1;
            if (c.html5Only)
                return ra(), (c.oMC = W(c.movieID)), ma(), (L = K = !0), !1;
            var a = e || c.url,
                f = c.altURL || a,
                h = va(),
                g = I(),
                l = null,
                l = n.getElementsByTagName("html")[0],
                m,
                p,
                q,
                l = l && l.dir && l.dir.match(/rtl/i);
            b = b === k ? c.id : b;
            ra();
            c.url = Qa(Ha ? a : f);
            e = c.url;
            c.wmode = !c.wmode && c.useHighPerformance ? "transparent" : c.wmode;
            if (
                null !== c.wmode &&
                (s.match(/msie 8/i) || (!C && !c.useHighPerformance)) &&
                navigator.platform.match(/win32|win64/i)
            )
                Ua.push(sa.spcWmode), (c.wmode = null);
            h = {
                name: b,
                id: b,
                src: e,
                quality: "high",
                allowScriptAccess: c.allowScriptAccess,
                bgcolor: c.bgColor,
                pluginspage: cb + "www.macromedia.com/go/getflashplayer",
                title: "JS/Flash audio component (SoundManager 2)",
                type: "application/x-shockwave-flash",
                wmode: c.wmode,
                hasPriority: "true"
            };
            c.debugFlash && (h.FlashVars = "debug\x3d1");
            c.wmode || delete h.wmode;
            if (C)
                (a = n.createElement("div")),
                (p = [
                    '\x3cobject id\x3d"' +
                    b +
                    '" data\x3d"' +
                    e +
                    '" type\x3d"' +
                    h.type +
                    '" title\x3d"' +
                    h.title +
                    '" classid\x3d"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase\x3d"' +
                    cb +
                    'download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version\x3d6,0,40,0"\x3e',
                    d("movie", e),
                    d("AllowScriptAccess", c.allowScriptAccess),
                    d("quality", h.quality),
                    c.wmode ? d("wmode", c.wmode) : "",
                    d("bgcolor", c.bgColor),
                    d("hasPriority", "true"),
                    c.debugFlash ? d("FlashVars", h.FlashVars) : "",
                    "\x3c/object\x3e"
                ].join(""));
            else
                for (m in ((a = n.createElement("embed")), h))
                    h.hasOwnProperty(m) && a.setAttribute(m, h[m]);
            wa();
            g = I();
            if ((h = va()))
                if (
                    ((c.oMC = W(c.movieID) || n.createElement("div")), c.oMC.id)
                )
                    (q = c.oMC.className),
                    (c.oMC.className =
                        (q ? q + " " : "movieContainer") +
                        (g ? " " + g : "")),
                    c.oMC.appendChild(a),
                    C &&
                    ((m = c.oMC.appendChild(n.createElement("div"))),
                        (m.className = "sm2-object-box"),
                        (m.innerHTML = p)),
                    (L = !0);
                else {
                    c.oMC.id = c.movieID;
                    c.oMC.className = "movieContainer " + g;
                    m = g = null;
                    c.useFlashBlock ||
                        (c.useHighPerformance ?
                            (g = {
                                position: "fixed",
                                width: "8px",
                                height: "8px",
                                bottom: "0px",
                                left: "0px",
                                overflow: "hidden"
                            }) :
                            ((g = {
                                    position: "absolute",
                                    width: "6px",
                                    height: "6px",
                                    top: "-9999px",
                                    left: "-9999px"
                                }),
                                l &&
                                (g.left =
                                    Math.abs(parseInt(g.left, 10)) + "px")));
                    gb && (c.oMC.style.zIndex = 1e4);
                    if (!c.debugFlash)
                        for (q in g)
                            g.hasOwnProperty(q) && (c.oMC.style[q] = g[q]);
                    try {
                        C || c.oMC.appendChild(a),
                            h.appendChild(c.oMC),
                            C &&
                            ((m = c.oMC.appendChild(
                                    n.createElement("div")
                                )),
                                (m.className = "sm2-object-box"),
                                (m.innerHTML = p)),
                            (L = !0);
                    } catch (r) {
                        throw Error(P("domError") + " \n" + r.toString());
                    }
                }
            return (K = !0);
        };
        Z = function() {
            if (c.html5Only) return $(), !1;
            if (l || !c.url) return !1;
            l = c.getMovie(c.id);
            l ||
                (O ?
                    (C ? (c.oMC.innerHTML = ya) : c.oMC.appendChild(O),
                        (O = null),
                        (K = !0)) :
                    $(c.id, c.url),
                    (l = c.getMovie(c.id)));
            "function" === typeof c.oninitmovie && setTimeout(c.oninitmovie, 1);
            return !0;
        };
        F = function() {
            setTimeout(Na, 1e3);
        };
        qa = function() {
            g.setTimeout(function() {
                c.setup({ preferFlash: !1 }).reboot();
                c.didFlashBlock = !0;
                c.beginDelayedInit();
            }, 1);
        };
        Na = function() {
            var b,
                e = !1;
            if (!c.url || Q) return !1;
            Q = !0;
            t.remove(g, "load", F);
            if (v && ja && !Ga) return !1;
            p || ((b = c.getMoviePercent()), 0 < b && 100 > b && (e = !0));
            setTimeout(function() {
                b = c.getMoviePercent();
                if (e) return (Q = !1), g.setTimeout(F, 1), !1;
                !p &&
                    ab &&
                    (null === b ?
                        c.useFlashBlock || 0 === c.flashLoadTimeout ?
                        c.useFlashBlock && za() :
                        !c.useFlashBlock && S ?
                        qa() :
                        D({
                            type: "ontimeout",
                            ignoreInit: !0,
                            error: { type: "INIT_FLASHBLOCK" }
                        }) :
                        0 !== c.flashLoadTimeout &&
                        (!c.useFlashBlock && S ? qa() : xa(!0)));
            }, c.flashLoadTimeout);
        };
        Y = function() {
            if (Ga || !ja) return t.remove(g, "focus", Y), !0;
            Ga = ab = !0;
            Q = !1;
            F();
            t.remove(g, "focus", Y);
            return !0;
        };
        M = function(b) {
            if (p) return !1;
            if (c.html5Only) return (p = !0), E(), !0;
            var e = !0,
                d;
            if (!c.useFlashBlock || !c.flashLoadTimeout || c.getMoviePercent())
                p = !0;
            d = { type: !v && u ? "NO_FLASH" : "INIT_TIMEOUT" };
            if (y || b)
                c.useFlashBlock &&
                c.oMC &&
                (c.oMC.className =
                    I() +
                    " " +
                    (null === c.getMoviePercent() ?
                        "swf_timedout" :
                        "swf_error")),
                D({ type: "ontimeout", error: d, ignoreInit: !0 }),
                H(d),
                (e = !1);
            y || (c.waitForWindowLoad && !na ? t.add(g, "load", E) : E());
            return e;
        };
        Ma = function() {
            var b,
                e = c.setupOptions;
            for (b in e)
                e.hasOwnProperty(b) &&
                (c[b] === k ?
                    (c[b] = e[b]) :
                    c[b] !== e[b] && (c.setupOptions[b] = c[b]));
        };
        ma = function() {
            if (p) return !1;
            if (c.html5Only)
                return (
                    p ||
                    (t.remove(g, "load", c.beginDelayedInit),
                        (c.enabled = !0),
                        M()), !0
                );
            Z();
            try {
                l._externalInterfaceTest(!1),
                    Oa(!0,
                        c.flashPollingInterval ||
                        (c.useHighPerformance ? 10 : 50)
                    ),
                    c.debugMode || l._disableDebug(),
                    (c.enabled = !0),
                    c.html5Only || t.add(g, "unload", la);
            } catch (b) {
                return (
                    H({ type: "JS_TO_FLASH_EXCEPTION", fatal: !0 }),
                    xa(!0),
                    M(), !1
                );
            }
            M();
            t.remove(g, "load", c.beginDelayedInit);
            return !0;
        };
        G = function() {
            if (N) return !1;
            N = !0;
            Ma();
            wa();
            !v && c.hasHTML5 && c.setup({ useHTML5Audio: !0, preferFlash: !1 });
            Wa();
            !v &&
                u &&
                (Ua.push(sa.needFlash), c.setup({ flashLoadTimeout: 1 }));
            n.removeEventListener &&
                n.removeEventListener("DOMContentLoaded", G, !1);
            Z();
            return !0;
        };
        Ba = function() {
            "complete" === n.readyState &&
                (G(), n.detachEvent("onreadystatechange", Ba));
            return !0;
        };
        ua = function() {
            na = !0;
            t.remove(g, "load", ua);
        };
        ta = function() {
            if (
                Fa &&
                ((c.setupOptions.useHTML5Audio = !0),
                    (c.setupOptions.preferFlash = !1),
                    ha || (Za && !s.match(/android\s2\.3/i)))
            )
                ha && (c.ignoreFlash = !0), (A = !0);
        };
        ta();
        Da();
        t.add(g, "focus", Y);
        t.add(g, "load", F);
        t.add(g, "load", ua);
        n.addEventListener ?
            n.addEventListener("DOMContentLoaded", G, !1) :
            n.attachEvent ?
            n.attachEvent("onreadystatechange", Ba) :
            H({ type: "NO_DOM2_EVENTS", fatal: !0 });
    }
    var ka = null;
    if (void 0 === g.SM2_DEFER || !SM2_DEFER) ka = new U();
    g.SoundManager = U;
    g.soundManager = ka;
})(window);

/*
        Copyright (c) 2008, Yahoo! Inc. All rights reserved.
        Code licensed under the BSD License:
        http://developer.yahoo.net/yui/license.txt
        version: 2.6.0
        */
if (typeof YAHOO == "undefined" || !YAHOO) {
    var YAHOO = {};
}
YAHOO.namespace = function() {
    var A = arguments,
        E = null,
        C,
        B,
        D;
    for (C = 0; C < A.length; C = C + 1) {
        D = A[C].split(".");
        E = YAHOO;
        for (B = D[0] == "YAHOO" ? 1 : 0; B < D.length; B = B + 1) {
            E[D[B]] = E[D[B]] || {};
            E = E[D[B]];
        }
    }
    return E;
};
YAHOO.log = function(D, A, C) {
    var B = YAHOO.widget.Logger;
    if (B && B.log) {
        return B.log(D, A, C);
    } else {
        return false;
    }
};
YAHOO.register = function(A, E, D) {
    var I = YAHOO.env.modules;
    if (!I[A]) {
        I[A] = { versions: [], builds: [] };
    }
    var B = I[A],
        H = D.version,
        G = D.build,
        F = YAHOO.env.listeners;
    B.name = A;
    B.version = H;
    B.build = G;
    B.versions.push(H);
    B.builds.push(G);
    B.mainClass = E;
    for (var C = 0; C < F.length; C = C + 1) {
        F[C](B);
    }
    if (E) {
        E.VERSION = H;
        E.BUILD = G;
    } else {
        YAHOO.log("mainClass is undefined for module " + A, "warn");
    }
};
YAHOO.env = YAHOO.env || { modules: [], listeners: [] };
YAHOO.env.getVersion = function(A) {
    return YAHOO.env.modules[A] || null;
};
YAHOO.env.ua = (function() {
    var C = { ie: 0, opera: 0, gecko: 0, webkit: 0, mobile: null, air: 0 };
    var B = navigator.userAgent,
        A;
    if (/KHTML/.test(B)) {
        C.webkit = 1;
    }
    A = B.match(/AppleWebKit\/([^\s]*)/);
    if (A && A[1]) {
        C.webkit = parseFloat(A[1]);
        if (/ Mobile\//.test(B)) {
            C.mobile = "Apple";
        } else {
            A = B.match(/NokiaN[^\/]*/);
            if (A) {
                C.mobile = A[0];
            }
        }
        A = B.match(/AdobeAIR\/([^\s]*)/);
        if (A) {
            C.air = A[0];
        }
    }
    if (!C.webkit) {
        A = B.match(/Opera[\s\/]([^\s]*)/);
        if (A && A[1]) {
            C.opera = parseFloat(A[1]);
            A = B.match(/Opera Mini[^;]*/);
            if (A) {
                C.mobile = A[0];
            }
        } else {
            A = B.match(/MSIE\s([^;]*)/);
            if (A && A[1]) {
                C.ie = parseFloat(A[1]);
            } else {
                A = B.match(/Gecko\/([^\s]*)/);
                if (A) {
                    C.gecko = 1;
                    A = B.match(/rv:([^\s\)]*)/);
                    if (A && A[1]) {
                        C.gecko = parseFloat(A[1]);
                    }
                }
            }
        }
    }
    return C;
})();
(function() {
    YAHOO.namespace("util", "widget", "example");
    if ("undefined" !== typeof YAHOO_config) {
        var B = YAHOO_config.listener,
            A = YAHOO.env.listeners,
            D = true,
            C;
        if (B) {
            for (C = 0; C < A.length; C = C + 1) {
                if (A[C] == B) {
                    D = false;
                    break;
                }
            }
            if (D) {
                A.push(B);
            }
        }
    }
})();
YAHOO.lang = YAHOO.lang || {};
(function() {
    var A = YAHOO.lang,
        C = ["toString", "valueOf"],
        B = {
            isArray: function(D) {
                if (D) {
                    return A.isNumber(D.length) && A.isFunction(D.splice);
                }
                return false;
            },
            isBoolean: function(D) {
                return typeof D === "boolean";
            },
            isFunction: function(D) {
                return typeof D === "function";
            },
            isNull: function(D) {
                return D === null;
            },
            isNumber: function(D) {
                return typeof D === "number" && isFinite(D);
            },
            isObject: function(D) {
                return (
                    (D && (typeof D === "object" || A.isFunction(D))) || false
                );
            },
            isString: function(D) {
                return typeof D === "string";
            },
            isUndefined: function(D) {
                return typeof D === "undefined";
            },
            _IEEnumFix: YAHOO.env.ua.ie ?

                function(F, E) {
                    for (var D = 0; D < C.length; D = D + 1) {
                        var H = C[D],
                            G = E[H];
                        if (A.isFunction(G) && G != Object.prototype[H]) {
                            F[H] = G;
                        }
                    }
                } : function() {},
            extend: function(H, I, G) {
                if (!I || !H) {
                    throw new Error(
                        "extend failed, please check that " +
                        "all dependencies are included."
                    );
                }
                var E = function() {};
                E.prototype = I.prototype;
                H.prototype = new E();
                H.prototype.constructor = H;
                H.superclass = I.prototype;
                if (I.prototype.constructor == Object.prototype.constructor) {
                    I.prototype.constructor = I;
                }
                if (G) {
                    for (var D in G) {
                        if (A.hasOwnProperty(G, D)) {
                            H.prototype[D] = G[D];
                        }
                    }
                    A._IEEnumFix(H.prototype, G);
                }
            },
            augmentObject: function(H, G) {
                if (!G || !H) {
                    throw new Error("Absorb failed, verify dependencies.");
                }
                var D = arguments,
                    F,
                    I,
                    E = D[2];
                if (E && E !== true) {
                    for (F = 2; F < D.length; F = F + 1) {
                        H[D[F]] = G[D[F]];
                    }
                } else {
                    for (I in G) {
                        if (E || !(I in H)) {
                            H[I] = G[I];
                        }
                    }
                    A._IEEnumFix(H, G);
                }
            },
            augmentProto: function(G, F) {
                if (!F || !G) {
                    throw new Error("Augment failed, verify dependencies.");
                }
                var D = [G.prototype, F.prototype];
                for (var E = 2; E < arguments.length; E = E + 1) {
                    D.push(arguments[E]);
                }
                A.augmentObject.apply(this, D);
            },
            dump: function(D, I) {
                var F,
                    H,
                    K = [],
                    L = "{...}",
                    E = "f(){...}",
                    J = ", ",
                    G = " => ";
                if (!A.isObject(D)) {
                    return D + "";
                } else {
                    if (
                        D instanceof Date ||
                        ("nodeType" in D && "tagName" in D)
                    ) {
                        return D;
                    } else {
                        if (A.isFunction(D)) {
                            return E;
                        }
                    }
                }
                I = A.isNumber(I) ? I : 3;
                if (A.isArray(D)) {
                    K.push("[");
                    for (F = 0, H = D.length; F < H; F = F + 1) {
                        if (A.isObject(D[F])) {
                            K.push(I > 0 ? A.dump(D[F], I - 1) : L);
                        } else {
                            K.push(D[F]);
                        }
                        K.push(J);
                    }
                    if (K.length > 1) {
                        K.pop();
                    }
                    K.push("]");
                } else {
                    K.push("{");
                    for (F in D) {
                        if (A.hasOwnProperty(D, F)) {
                            K.push(F + G);
                            if (A.isObject(D[F])) {
                                K.push(I > 0 ? A.dump(D[F], I - 1) : L);
                            } else {
                                K.push(D[F]);
                            }
                            K.push(J);
                        }
                    }
                    if (K.length > 1) {
                        K.pop();
                    }
                    K.push("}");
                }
                return K.join("");
            },
            substitute: function(S, E, L) {
                var I,
                    H,
                    G,
                    O,
                    P,
                    R,
                    N = [],
                    F,
                    J = "dump",
                    M = " ",
                    D = "{",
                    Q = "}";
                for (;;) {
                    I = S.lastIndexOf(D);
                    if (I < 0) {
                        break;
                    }
                    H = S.indexOf(Q, I);
                    if (I + 1 >= H) {
                        break;
                    }
                    F = S.substring(I + 1, H);
                    O = F;
                    R = null;
                    G = O.indexOf(M);
                    if (G > -1) {
                        R = O.substring(G + 1);
                        O = O.substring(0, G);
                    }
                    P = E[O];
                    if (L) {
                        P = L(O, P, R);
                    }
                    if (A.isObject(P)) {
                        if (A.isArray(P)) {
                            P = A.dump(P, parseInt(R, 10));
                        } else {
                            R = R || "";
                            var K = R.indexOf(J);
                            if (K > -1) {
                                R = R.substring(4);
                            }
                            if (
                                P.toString === Object.prototype.toString ||
                                K > -1
                            ) {
                                P = A.dump(P, parseInt(R, 10));
                            } else {
                                P = P.toString();
                            }
                        }
                    } else {
                        if (!A.isString(P) && !A.isNumber(P)) {
                            P = "~-" + N.length + "-~";
                            N[N.length] = F;
                        }
                    }
                    S = S.substring(0, I) + P + S.substring(H + 1);
                }
                for (I = N.length - 1; I >= 0; I = I - 1) {
                    S = S.replace(
                        new RegExp("~-" + I + "-~"),
                        "{" + N[I] + "}",
                        "g"
                    );
                }
                return S;
            },
            trim: function(D) {
                try {
                    return D.replace(/^\s+|\s+$/g, "");
                } catch (E) {
                    return D;
                }
            },
            merge: function() {
                var G = {},
                    E = arguments;
                for (var F = 0, D = E.length; F < D; F = F + 1) {
                    A.augmentObject(G, E[F], true);
                }
                return G;
            },
            later: function(K, E, L, G, H) {
                K = K || 0;
                E = E || {};
                var F = L,
                    J = G,
                    I,
                    D;
                if (A.isString(L)) {
                    F = E[L];
                }
                if (!F) {
                    throw new TypeError("method undefined");
                }
                if (!A.isArray(J)) {
                    J = [G];
                }
                I = function() {
                    F.apply(E, J);
                };
                D = H ? setInterval(I, K) : setTimeout(I, K);
                return {
                    interval: H,
                    cancel: function() {
                        if (this.interval) {
                            clearInterval(D);
                        } else {
                            clearTimeout(D);
                        }
                    }
                };
            },
            isValue: function(D) {
                return (
                    A.isObject(D) ||
                    A.isString(D) ||
                    A.isNumber(D) ||
                    A.isBoolean(D)
                );
            }
        };
    A.hasOwnProperty = Object.prototype.hasOwnProperty ?

        function(D, E) {
            return D && D.hasOwnProperty(E);
        } :
        function(D, E) {
            return (!A.isUndefined(D[E]) && D.constructor.prototype[E] !== D[E]);
        };
    B.augmentObject(A, B, true);
    YAHOO.util.Lang = A;
    A.augment = A.augmentProto;
    YAHOO.augment = A.augmentProto;
    YAHOO.extend = A.extend;
})();
YAHOO.register("yahoo", YAHOO, { version: "2.6.0", build: "1321" });
(function() {
    var B = YAHOO.util,
        F = YAHOO.lang,
        L,
        J,
        K = {},
        G = {},
        N = window.document;
    YAHOO.env._id_counter = YAHOO.env._id_counter || 0;
    var C = YAHOO.env.ua.opera,
        M = YAHOO.env.ua.webkit,
        A = YAHOO.env.ua.gecko,
        H = YAHOO.env.ua.ie;
    var E = {
        HYPHEN: /(-[a-z])/i,
        ROOT_TAG: /^body|html$/i,
        OP_SCROLL: /^(?:inline|table-row)$/i
    };
    var O = function(Q) {
        if (!E.HYPHEN.test(Q)) {
            return Q;
        }
        if (K[Q]) {
            return K[Q];
        }
        var R = Q;
        while (E.HYPHEN.exec(R)) {
            R = R.replace(RegExp.$1, RegExp.$1.substr(1).toUpperCase());
        }
        K[Q] = R;
        return R;
    };
    var P = function(R) {
        var Q = G[R];
        if (!Q) {
            Q = new RegExp("(?:^|\\s+)" + R + "(?:\\s+|$)");
            G[R] = Q;
        }
        return Q;
    };
    if (N.defaultView && N.defaultView.getComputedStyle) {
        L = function(Q, T) {
            var S = null;
            if (T == "float") {
                T = "cssFloat";
            }
            var R = Q.ownerDocument.defaultView.getComputedStyle(Q, "");
            if (R) {
                S = R[O(T)];
            }
            return Q.style[T] || S;
        };
    } else {
        if (N.documentElement.currentStyle && H) {
            L = function(Q, S) {
                switch (O(S)) {
                    case "opacity":
                        var U = 100;
                        try {
                            U =
                                Q.filters["DXImageTransform.Microsoft.Alpha"]
                                .opacity;
                        } catch (T) {
                            try {
                                U = Q.filters("alpha").opacity;
                            } catch (T) {}
                        }
                        return U / 100;
                    case "float":
                        S = "styleFloat";
                    default:
                        var R = Q.currentStyle ? Q.currentStyle[S] : null;
                        return Q.style[S] || R;
                }
            };
        } else {
            L = function(Q, R) {
                return Q.style[R];
            };
        }
    }
    if (H) {
        J = function(Q, R, S) {
            switch (R) {
                case "opacity":
                    if (F.isString(Q.style.filter)) {
                        Q.style.filter = "alpha(opacity=" + S * 100 + ")";
                        if (!Q.currentStyle || !Q.currentStyle.hasLayout) {
                            Q.style.zoom = 1;
                        }
                    }
                    break;
                case "float":
                    R = "styleFloat";
                default:
                    Q.style[R] = S;
            }
        };
    } else {
        J = function(Q, R, S) {
            if (R == "float") {
                R = "cssFloat";
            }
            Q.style[R] = S;
        };
    }
    var D = function(Q, R) {
        return Q && Q.nodeType == 1 && (!R || R(Q));
    };
    YAHOO.util.Dom = {
        get: function(S) {
            if (S) {
                if (S.nodeType || S.item) {
                    return S;
                }
                if (typeof S === "string") {
                    return N.getElementById(S);
                }
                if ("length" in S) {
                    var T = [];
                    for (var R = 0, Q = S.length; R < Q; ++R) {
                        T[T.length] = B.Dom.get(S[R]);
                    }
                    return T;
                }
                return S;
            }
            return null;
        },
        getStyle: function(Q, S) {
            S = O(S);
            var R = function(T) {
                return L(T, S);
            };
            return B.Dom.batch(Q, R, B.Dom, true);
        },
        setStyle: function(Q, S, T) {
            S = O(S);
            var R = function(U) {
                J(U, S, T);
            };
            B.Dom.batch(Q, R, B.Dom, true);
        },
        getXY: function(Q) {
            var R = function(S) {
                if (
                    (S.parentNode === null ||
                        S.offsetParent === null ||
                        this.getStyle(S, "display") == "none") &&
                    S != S.ownerDocument.body
                ) {
                    return false;
                }
                return I(S);
            };
            return B.Dom.batch(Q, R, B.Dom, true);
        },
        getX: function(Q) {
            var R = function(S) {
                return B.Dom.getXY(S)[0];
            };
            return B.Dom.batch(Q, R, B.Dom, true);
        },
        getY: function(Q) {
            var R = function(S) {
                return B.Dom.getXY(S)[1];
            };
            return B.Dom.batch(Q, R, B.Dom, true);
        },
        setXY: function(Q, T, S) {
            var R = function(W) {
                var V = this.getStyle(W, "position");
                if (V == "static") {
                    this.setStyle(W, "position", "relative");
                    V = "relative";
                }
                var Y = this.getXY(W);
                if (Y === false) {
                    return false;
                }
                var X = [
                    parseInt(this.getStyle(W, "left"), 10),
                    parseInt(this.getStyle(W, "top"), 10)
                ];
                if (isNaN(X[0])) {
                    X[0] = V == "relative" ? 0 : W.offsetLeft;
                }
                if (isNaN(X[1])) {
                    X[1] = V == "relative" ? 0 : W.offsetTop;
                }
                if (T[0] !== null) {
                    W.style.left = T[0] - Y[0] + X[0] + "px";
                }
                if (T[1] !== null) {
                    W.style.top = T[1] - Y[1] + X[1] + "px";
                }
                if (!S) {
                    var U = this.getXY(W);
                    if (
                        (T[0] !== null && U[0] != T[0]) ||
                        (T[1] !== null && U[1] != T[1])
                    ) {
                        this.setXY(W, T, true);
                    }
                }
            };
            B.Dom.batch(Q, R, B.Dom, true);
        },
        setX: function(R, Q) {
            B.Dom.setXY(R, [Q, null]);
        },
        setY: function(Q, R) {
            B.Dom.setXY(Q, [null, R]);
        },
        getRegion: function(Q) {
            var R = function(S) {
                if (
                    (S.parentNode === null ||
                        S.offsetParent === null ||
                        this.getStyle(S, "display") == "none") &&
                    S != S.ownerDocument.body
                ) {
                    return false;
                }
                var T = B.Region.getRegion(S);
                return T;
            };
            return B.Dom.batch(Q, R, B.Dom, true);
        },
        getClientWidth: function() {
            return B.Dom.getViewportWidth();
        },
        getClientHeight: function() {
            return B.Dom.getViewportHeight();
        },
        getElementsByClassName: function(U, Y, V, W) {
            U = F.trim(U);
            Y = Y || "*";
            V = V ? B.Dom.get(V) : null || N;
            if (!V) {
                return [];
            }
            var R = [],
                Q = V.getElementsByTagName(Y),
                X = P(U);
            for (var S = 0, T = Q.length; S < T; ++S) {
                if (X.test(Q[S].className)) {
                    R[R.length] = Q[S];
                    if (W) {
                        W.call(Q[S], Q[S]);
                    }
                }
            }
            return R;
        },
        hasClass: function(S, R) {
            var Q = P(R);
            var T = function(U) {
                return Q.test(U.className);
            };
            return B.Dom.batch(S, T, B.Dom, true);
        },
        addClass: function(R, Q) {
            var S = function(T) {
                if (this.hasClass(T, Q)) {
                    return false;
                }
                T.className = F.trim([T.className, Q].join(" "));
                return true;
            };
            return B.Dom.batch(R, S, B.Dom, true);
        },
        removeClass: function(S, R) {
            var Q = P(R);
            var T = function(W) {
                var V = false,
                    X = W.className;
                if (R && X && this.hasClass(W, R)) {
                    W.className = X.replace(Q, " ");
                    if (this.hasClass(W, R)) {
                        this.removeClass(W, R);
                    }
                    W.className = F.trim(W.className);
                    if (W.className === "") {
                        var U = W.hasAttribute ? "class" : "className";
                        W.removeAttribute(U);
                    }
                    V = true;
                }
                return V;
            };
            return B.Dom.batch(S, T, B.Dom, true);
        },
        replaceClass: function(T, R, Q) {
            if (!Q || R === Q) {
                return false;
            }
            var S = P(R);
            var U = function(V) {
                if (!this.hasClass(V, R)) {
                    this.addClass(V, Q);
                    return true;
                }
                V.className = V.className.replace(S, " " + Q + " ");
                if (this.hasClass(V, R)) {
                    this.removeClass(V, R);
                }
                V.className = F.trim(V.className);
                return true;
            };
            return B.Dom.batch(T, U, B.Dom, true);
        },
        generateId: function(Q, S) {
            S = S || "yui-gen";
            var R = function(T) {
                if (T && T.id) {
                    return T.id;
                }
                var U = S + YAHOO.env._id_counter++;
                if (T) {
                    T.id = U;
                }
                return U;
            };
            return B.Dom.batch(Q, R, B.Dom, true) || R.apply(B.Dom, arguments);
        },
        isAncestor: function(R, S) {
            R = B.Dom.get(R);
            S = B.Dom.get(S);
            var Q = false;
            if (R && S && R.nodeType && S.nodeType) {
                if (R.contains && R !== S) {
                    Q = R.contains(S);
                } else {
                    if (R.compareDocumentPosition) {
                        Q = !!(R.compareDocumentPosition(S) & 16);
                    }
                }
            } else {}
            return Q;
        },
        inDocument: function(Q) {
            return this.isAncestor(N.documentElement, Q);
        },
        getElementsBy: function(X, R, S, U) {
            R = R || "*";
            S = S ? B.Dom.get(S) : null || N;
            if (!S) {
                return [];
            }
            var T = [],
                W = S.getElementsByTagName(R);
            for (var V = 0, Q = W.length; V < Q; ++V) {
                if (X(W[V])) {
                    T[T.length] = W[V];
                    if (U) {
                        U(W[V]);
                    }
                }
            }
            return T;
        },
        batch: function(U, X, W, S) {
            U = U && (U.tagName || U.item) ? U : B.Dom.get(U);
            if (!U || !X) {
                return false;
            }
            var T = S ? W : window;
            if (U.tagName || U.length === undefined) {
                return X.call(T, U, W);
            }
            var V = [];
            for (var R = 0, Q = U.length; R < Q; ++R) {
                V[V.length] = X.call(T, U[R], W);
            }
            return V;
        },
        getDocumentHeight: function() {
            var R =
                N.compatMode != "CSS1Compat" ?
                N.body.scrollHeight :
                N.documentElement.scrollHeight;
            var Q = Math.max(R, B.Dom.getViewportHeight());
            return Q;
        },
        getDocumentWidth: function() {
            var R =
                N.compatMode != "CSS1Compat" ?
                N.body.scrollWidth :
                N.documentElement.scrollWidth;
            var Q = Math.max(R, B.Dom.getViewportWidth());
            return Q;
        },
        getViewportHeight: function() {
            var Q = self.innerHeight;
            var R = N.compatMode;
            if ((R || H) && !C) {
                Q =
                    R == "CSS1Compat" ?
                    N.documentElement.clientHeight :
                    N.body.clientHeight;
            }
            return Q;
        },
        getViewportWidth: function() {
            var Q = self.innerWidth;
            var R = N.compatMode;
            if (R || H) {
                Q =
                    R == "CSS1Compat" ?
                    N.documentElement.clientWidth :
                    N.body.clientWidth;
            }
            return Q;
        },
        getAncestorBy: function(Q, R) {
            while ((Q = Q.parentNode)) {
                if (D(Q, R)) {
                    return Q;
                }
            }
            return null;
        },
        getAncestorByClassName: function(R, Q) {
            R = B.Dom.get(R);
            if (!R) {
                return null;
            }
            var S = function(T) {
                return B.Dom.hasClass(T, Q);
            };
            return B.Dom.getAncestorBy(R, S);
        },
        getAncestorByTagName: function(R, Q) {
            R = B.Dom.get(R);
            if (!R) {
                return null;
            }
            var S = function(T) {
                return T.tagName && T.tagName.toUpperCase() == Q.toUpperCase();
            };
            return B.Dom.getAncestorBy(R, S);
        },
        getPreviousSiblingBy: function(Q, R) {
            while (Q) {
                Q = Q.previousSibling;
                if (D(Q, R)) {
                    return Q;
                }
            }
            return null;
        },
        getPreviousSibling: function(Q) {
            Q = B.Dom.get(Q);
            if (!Q) {
                return null;
            }
            return B.Dom.getPreviousSiblingBy(Q);
        },
        getNextSiblingBy: function(Q, R) {
            while (Q) {
                Q = Q.nextSibling;
                if (D(Q, R)) {
                    return Q;
                }
            }
            return null;
        },
        getNextSibling: function(Q) {
            Q = B.Dom.get(Q);
            if (!Q) {
                return null;
            }
            return B.Dom.getNextSiblingBy(Q);
        },
        getFirstChildBy: function(Q, S) {
            var R = D(Q.firstChild, S) ? Q.firstChild : null;
            return R || B.Dom.getNextSiblingBy(Q.firstChild, S);
        },
        getFirstChild: function(Q, R) {
            Q = B.Dom.get(Q);
            if (!Q) {
                return null;
            }
            return B.Dom.getFirstChildBy(Q);
        },
        getLastChildBy: function(Q, S) {
            if (!Q) {
                return null;
            }
            var R = D(Q.lastChild, S) ? Q.lastChild : null;
            return R || B.Dom.getPreviousSiblingBy(Q.lastChild, S);
        },
        getLastChild: function(Q) {
            Q = B.Dom.get(Q);
            return B.Dom.getLastChildBy(Q);
        },
        getChildrenBy: function(R, T) {
            var S = B.Dom.getFirstChildBy(R, T);
            var Q = S ? [S] : [];
            B.Dom.getNextSiblingBy(S, function(U) {
                if (!T || T(U)) {
                    Q[Q.length] = U;
                }
                return false;
            });
            return Q;
        },
        getChildren: function(Q) {
            Q = B.Dom.get(Q);
            if (!Q) {}
            return B.Dom.getChildrenBy(Q);
        },
        getDocumentScrollLeft: function(Q) {
            Q = Q || N;
            return Math.max(Q.documentElement.scrollLeft, Q.body.scrollLeft);
        },
        getDocumentScrollTop: function(Q) {
            Q = Q || N;
            return Math.max(Q.documentElement.scrollTop, Q.body.scrollTop);
        },
        insertBefore: function(R, Q) {
            R = B.Dom.get(R);
            Q = B.Dom.get(Q);
            if (!R || !Q || !Q.parentNode) {
                return null;
            }
            return Q.parentNode.insertBefore(R, Q);
        },
        insertAfter: function(R, Q) {
            R = B.Dom.get(R);
            Q = B.Dom.get(Q);
            if (!R || !Q || !Q.parentNode) {
                return null;
            }
            if (Q.nextSibling) {
                return Q.parentNode.insertBefore(R, Q.nextSibling);
            } else {
                return Q.parentNode.appendChild(R);
            }
        },
        getClientRegion: function() {
            var S = B.Dom.getDocumentScrollTop(),
                R = B.Dom.getDocumentScrollLeft(),
                T = B.Dom.getViewportWidth() + R,
                Q = B.Dom.getViewportHeight() + S;
            return new B.Region(S, T, Q, R);
        }
    };
    var I = (function() {
        if (N.documentElement.getBoundingClientRect) {
            return function(S) {
                var T = S.getBoundingClientRect(),
                    R = Math.round;
                var Q = S.ownerDocument;
                return [
                    R(T.left + B.Dom.getDocumentScrollLeft(Q)),
                    R(T.top + B.Dom.getDocumentScrollTop(Q))
                ];
            };
        } else {
            return function(S) {
                var T = [S.offsetLeft, S.offsetTop];
                var R = S.offsetParent;
                var Q =
                    M &&
                    B.Dom.getStyle(S, "position") == "absolute" &&
                    S.offsetParent == S.ownerDocument.body;
                if (R != S) {
                    while (R) {
                        T[0] += R.offsetLeft;
                        T[1] += R.offsetTop;
                        if (!Q &&
                            M &&
                            B.Dom.getStyle(R, "position") == "absolute"
                        ) {
                            Q = true;
                        }
                        R = R.offsetParent;
                    }
                }
                if (Q) {
                    T[0] -= S.ownerDocument.body.offsetLeft;
                    T[1] -= S.ownerDocument.body.offsetTop;
                }
                R = S.parentNode;
                while (R.tagName && !E.ROOT_TAG.test(R.tagName)) {
                    if (R.scrollTop || R.scrollLeft) {
                        T[0] -= R.scrollLeft;
                        T[1] -= R.scrollTop;
                    }
                    R = R.parentNode;
                }
                return T;
            };
        }
    })();
})();
YAHOO.util.Region = function(C, D, A, B) {
    this.top = C;
    this[1] = C;
    this.right = D;
    this.bottom = A;
    this.left = B;
    this[0] = B;
};
YAHOO.util.Region.prototype.contains = function(A) {
    return (
        A.left >= this.left &&
        A.right <= this.right &&
        A.top >= this.top &&
        A.bottom <= this.bottom
    );
};
YAHOO.util.Region.prototype.getArea = function() {
    return (this.bottom - this.top) * (this.right - this.left);
};
YAHOO.util.Region.prototype.intersect = function(E) {
    var C = Math.max(this.top, E.top);
    var D = Math.min(this.right, E.right);
    var A = Math.min(this.bottom, E.bottom);
    var B = Math.max(this.left, E.left);
    if (A >= C && D >= B) {
        return new YAHOO.util.Region(C, D, A, B);
    } else {
        return null;
    }
};
YAHOO.util.Region.prototype.union = function(E) {
    var C = Math.min(this.top, E.top);
    var D = Math.max(this.right, E.right);
    var A = Math.max(this.bottom, E.bottom);
    var B = Math.min(this.left, E.left);
    return new YAHOO.util.Region(C, D, A, B);
};
YAHOO.util.Region.prototype.toString = function() {
    return (
        "Region {" +
        "top: " +
        this.top +
        ", right: " +
        this.right +
        ", bottom: " +
        this.bottom +
        ", left: " +
        this.left +
        "}"
    );
};
YAHOO.util.Region.getRegion = function(D) {
    var F = YAHOO.util.Dom.getXY(D);
    var C = F[1];
    var E = F[0] + D.offsetWidth;
    var A = F[1] + D.offsetHeight;
    var B = F[0];
    return new YAHOO.util.Region(C, E, A, B);
};
YAHOO.util.Point = function(A, B) {
    if (YAHOO.lang.isArray(A)) {
        B = A[1];
        A = A[0];
    }
    this.x = this.right = this.left = this[0] = A;
    this.y = this.top = this.bottom = this[1] = B;
};
YAHOO.util.Point.prototype = new YAHOO.util.Region();
YAHOO.register("dom", YAHOO.util.Dom, { version: "2.6.0", build: "1321" });
YAHOO.util.CustomEvent = function(D, B, C, A) {
    this.type = D;
    this.scope = B || window;
    this.silent = C;
    this.signature = A || YAHOO.util.CustomEvent.LIST;
    this.subscribers = [];
    if (!this.silent) {}
    var E = "_YUICEOnSubscribe";
    if (D !== E) {
        this.subscribeEvent = new YAHOO.util.CustomEvent(E, this, true);
    }
    this.lastError = null;
};
YAHOO.util.CustomEvent.LIST = 0;
YAHOO.util.CustomEvent.FLAT = 1;
YAHOO.util.CustomEvent.prototype = {
    subscribe: function(B, C, A) {
        if (!B) {
            throw new Error(
                "Invalid callback for subscriber to '" + this.type + "'"
            );
        }
        if (this.subscribeEvent) {
            this.subscribeEvent.fire(B, C, A);
        }
        this.subscribers.push(new YAHOO.util.Subscriber(B, C, A));
    },
    unsubscribe: function(D, F) {
        if (!D) {
            return this.unsubscribeAll();
        }
        var E = false;
        for (var B = 0, A = this.subscribers.length; B < A; ++B) {
            var C = this.subscribers[B];
            if (C && C.contains(D, F)) {
                this._delete(B);
                E = true;
            }
        }
        return E;
    },
    fire: function() {
        this.lastError = null;
        var K = [],
            E = this.subscribers.length;
        if (!E && this.silent) {
            return true;
        }
        var I = [].slice.call(arguments, 0),
            G = true,
            D,
            J = false;
        if (!this.silent) {}
        var C = this.subscribers.slice(),
            A = YAHOO.util.Event.throwErrors;
        for (D = 0; D < E; ++D) {
            var M = C[D];
            if (!M) {
                J = true;
            } else {
                if (!this.silent) {}
                var L = M.getScope(this.scope);
                if (this.signature == YAHOO.util.CustomEvent.FLAT) {
                    var B = null;
                    if (I.length > 0) {
                        B = I[0];
                    }
                    try {
                        G = M.fn.call(L, B, M.obj);
                    } catch (F) {
                        this.lastError = F;
                        if (A) {
                            throw F;
                        }
                    }
                } else {
                    try {
                        G = M.fn.call(L, this.type, I, M.obj);
                    } catch (H) {
                        this.lastError = H;
                        if (A) {
                            throw H;
                        }
                    }
                }
                if (false === G) {
                    if (!this.silent) {}
                    break;
                }
            }
        }
        return G !== false;
    },
    unsubscribeAll: function() {
        for (var A = this.subscribers.length - 1; A > -1; A--) {
            this._delete(A);
        }
        this.subscribers = [];
        return A;
    },
    _delete: function(A) {
        var B = this.subscribers[A];
        if (B) {
            delete B.fn;
            delete B.obj;
        }
        this.subscribers.splice(A, 1);
    },
    toString: function() {
        return (
            "CustomEvent: " + "'" + this.type + "', " + "scope: " + this.scope
        );
    }
};
YAHOO.util.Subscriber = function(B, C, A) {
    this.fn = B;
    this.obj = YAHOO.lang.isUndefined(C) ? null : C;
    this.override = A;
};
YAHOO.util.Subscriber.prototype.getScope = function(A) {
    if (this.override) {
        if (this.override === true) {
            return this.obj;
        } else {
            return this.override;
        }
    }
    return A;
};
YAHOO.util.Subscriber.prototype.contains = function(A, B) {
    if (B) {
        return this.fn == A && this.obj == B;
    } else {
        return this.fn == A;
    }
};
YAHOO.util.Subscriber.prototype.toString = function() {
    return (
        "Subscriber { obj: " +
        this.obj +
        ", override: " +
        (this.override || "no") +
        " }"
    );
};
if (!YAHOO.util.Event) {
    YAHOO.util.Event = (function() {
        var H = false;
        var I = [];
        var J = [];
        var G = [];
        var E = [];
        var C = 0;
        var F = [];
        var B = [];
        var A = 0;
        var D = {
            63232: 38,
            63233: 40,
            63234: 37,
            63235: 39,
            63276: 33,
            63277: 34,
            25: 9
        };
        var K = YAHOO.env.ua.ie ? "focusin" : "focus";
        var L = YAHOO.env.ua.ie ? "focusout" : "blur";
        return {
            POLL_RETRYS: 2000,
            POLL_INTERVAL: 20,
            EL: 0,
            TYPE: 1,
            FN: 2,
            WFN: 3,
            UNLOAD_OBJ: 3,
            ADJ_SCOPE: 4,
            OBJ: 5,
            OVERRIDE: 6,
            CAPTURE: 7,
            lastError: null,
            isSafari: YAHOO.env.ua.webkit,
            webkit: YAHOO.env.ua.webkit,
            isIE: YAHOO.env.ua.ie,
            _interval: null,
            _dri: null,
            DOMReady: false,
            throwErrors: false,
            startInterval: function() {
                if (!this._interval) {
                    var M = this;
                    var N = function() {
                        M._tryPreloadAttach();
                    };
                    this._interval = setInterval(N, this.POLL_INTERVAL);
                }
            },
            onAvailable: function(R, O, S, Q, P) {
                var M = YAHOO.lang.isString(R) ? [R] : R;
                for (var N = 0; N < M.length; N = N + 1) {
                    F.push({
                        id: M[N],
                        fn: O,
                        obj: S,
                        override: Q,
                        checkReady: P
                    });
                }
                C = this.POLL_RETRYS;
                this.startInterval();
            },
            onContentReady: function(O, M, P, N) {
                this.onAvailable(O, M, P, N, true);
            },
            onDOMReady: function(M, O, N) {
                if (this.DOMReady) {
                    setTimeout(function() {
                        var P = window;
                        if (N) {
                            if (N === true) {
                                P = O;
                            } else {
                                P = N;
                            }
                        }
                        M.call(P, "DOMReady", [], O);
                    }, 0);
                } else {
                    this.DOMReadyEvent.subscribe(M, O, N);
                }
            },
            _addListener: function(O, M, X, S, N, a) {
                if (!X || !X.call) {
                    return false;
                }
                if (this._isValidCollection(O)) {
                    var Y = true;
                    for (var T = 0, V = O.length; T < V; ++T) {
                        Y = this._addListener(O[T], M, X, S, N, a) && Y;
                    }
                    return Y;
                } else {
                    if (YAHOO.lang.isString(O)) {
                        var R = this.getEl(O);
                        if (R) {
                            O = R;
                        } else {
                            this.onAvailable(O, function() {
                                YAHOO.util.Event._addListener(O, M, X, S, N, a);
                            });
                            return true;
                        }
                    }
                }
                if (!O) {
                    return false;
                }
                if ("unload" == M && S !== this) {
                    J[J.length] = [O, M, X, S, N, a];
                    return true;
                }
                var b = O;
                if (N) {
                    if (N === true) {
                        b = S;
                    } else {
                        b = N;
                    }
                }
                var P = function(c) {
                    return X.call(b, YAHOO.util.Event.getEvent(c, O), S);
                };
                var Z = [O, M, X, P, b, S, N, a];
                var U = I.length;
                I[U] = Z;
                if (this.useLegacyEvent(O, M)) {
                    var Q = this.getLegacyIndex(O, M);
                    if (Q == -1 || O != G[Q][0]) {
                        Q = G.length;
                        B[O.id + M] = Q;
                        G[Q] = [O, M, O["on" + M]];
                        E[Q] = [];
                        O["on" + M] = function(c) {
                            YAHOO.util.Event.fireLegacyEvent(
                                YAHOO.util.Event.getEvent(c),
                                Q
                            );
                        };
                    }
                    E[Q].push(Z);
                } else {
                    try {
                        this._simpleAdd(O, M, P, a);
                    } catch (W) {
                        this.lastError = W;
                        this._removeListener(O, M, X, a);
                        return false;
                    }
                }
                return true;
            },
            addListener: function(O, Q, N, P, M) {
                return this._addListener(O, Q, N, P, M, false);
            },
            addFocusListener: function(O, N, P, M) {
                return this._addListener(O, K, N, P, M, true);
            },
            removeFocusListener: function(N, M) {
                return this._removeListener(N, K, M, true);
            },
            addBlurListener: function(O, N, P, M) {
                return this._addListener(O, L, N, P, M, true);
            },
            removeBlurListener: function(N, M) {
                return this._removeListener(N, L, M, true);
            },
            fireLegacyEvent: function(Q, O) {
                var S = true,
                    M,
                    U,
                    T,
                    V,
                    R;
                U = E[O].slice();
                for (var N = 0, P = U.length; N < P; ++N) {
                    T = U[N];
                    if (T && T[this.WFN]) {
                        V = T[this.ADJ_SCOPE];
                        R = T[this.WFN].call(V, Q);
                        S = S && R;
                    }
                }
                M = G[O];
                if (M && M[2]) {
                    M[2](Q);
                }
                return S;
            },
            getLegacyIndex: function(N, O) {
                var M = this.generateId(N) + O;
                if (typeof B[M] == "undefined") {
                    return -1;
                } else {
                    return B[M];
                }
            },
            useLegacyEvent: function(M, N) {
                return (
                    this.webkit &&
                    this.webkit < 419 &&
                    ("click" == N || "dblclick" == N)
                );
            },
            _removeListener: function(N, M, V, Y) {
                var Q, T, X;
                if (typeof N == "string") {
                    N = this.getEl(N);
                } else {
                    if (this._isValidCollection(N)) {
                        var W = true;
                        for (Q = N.length - 1; Q > -1; Q--) {
                            W = this._removeListener(N[Q], M, V, Y) && W;
                        }
                        return W;
                    }
                }
                if (!V || !V.call) {
                    return this.purgeElement(N, false, M);
                }
                if ("unload" == M) {
                    for (Q = J.length - 1; Q > -1; Q--) {
                        X = J[Q];
                        if (X && X[0] == N && X[1] == M && X[2] == V) {
                            J.splice(Q, 1);
                            return true;
                        }
                    }
                    return false;
                }
                var R = null;
                var S = arguments[4];
                if ("undefined" === typeof S) {
                    S = this._getCacheIndex(N, M, V);
                }
                if (S >= 0) {
                    R = I[S];
                }
                if (!N || !R) {
                    return false;
                }
                if (this.useLegacyEvent(N, M)) {
                    var P = this.getLegacyIndex(N, M);
                    var O = E[P];
                    if (O) {
                        for (Q = 0, T = O.length; Q < T; ++Q) {
                            X = O[Q];
                            if (
                                X &&
                                X[this.EL] == N &&
                                X[this.TYPE] == M &&
                                X[this.FN] == V
                            ) {
                                O.splice(Q, 1);
                                break;
                            }
                        }
                    }
                } else {
                    try {
                        this._simpleRemove(N, M, R[this.WFN], Y);
                    } catch (U) {
                        this.lastError = U;
                        return false;
                    }
                }
                delete I[S][this.WFN];
                delete I[S][this.FN];
                I.splice(S, 1);
                return true;
            },
            removeListener: function(N, O, M) {
                return this._removeListener(N, O, M, false);
            },
            getTarget: function(O, N) {
                var M = O.target || O.srcElement;
                return this.resolveTextNode(M);
            },
            resolveTextNode: function(N) {
                try {
                    if (N && 3 == N.nodeType) {
                        return N.parentNode;
                    }
                } catch (M) {}
                return N;
            },
            getPageX: function(N) {
                var M = N.pageX;
                if (!M && 0 !== M) {
                    M = N.clientX || 0;
                    if (this.isIE) {
                        M += this._getScrollLeft();
                    }
                }
                return M;
            },
            getPageY: function(M) {
                var N = M.pageY;
                if (!N && 0 !== N) {
                    N = M.clientY || 0;
                    if (this.isIE) {
                        N += this._getScrollTop();
                    }
                }
                return N;
            },
            getXY: function(M) {
                return [this.getPageX(M), this.getPageY(M)];
            },
            getRelatedTarget: function(N) {
                var M = N.relatedTarget;
                if (!M) {
                    if (N.type == "mouseout") {
                        M = N.toElement;
                    } else {
                        if (N.type == "mouseover") {
                            M = N.fromElement;
                        }
                    }
                }
                return this.resolveTextNode(M);
            },
            getTime: function(O) {
                if (!O.time) {
                    var N = new Date().getTime();
                    try {
                        O.time = N;
                    } catch (M) {
                        this.lastError = M;
                        return N;
                    }
                }
                return O.time;
            },
            stopEvent: function(M) {
                this.stopPropagation(M);
                this.preventDefault(M);
            },
            stopPropagation: function(M) {
                if (M.stopPropagation) {
                    M.stopPropagation();
                } else {
                    M.cancelBubble = true;
                }
            },
            preventDefault: function(M) {
                if (M.preventDefault) {
                    M.preventDefault();
                } else {
                    M.returnValue = false;
                }
            },
            getEvent: function(O, M) {
                var N = O || window.event;
                if (!N) {
                    var P = this.getEvent.caller;
                    while (P) {
                        N = P.arguments[0];
                        if (N && Event == N.constructor) {
                            break;
                        }
                        P = P.caller;
                    }
                }
                return N;
            },
            getCharCode: function(N) {
                var M = N.keyCode || N.charCode || 0;
                if (YAHOO.env.ua.webkit && M in D) {
                    M = D[M];
                }
                return M;
            },
            _getCacheIndex: function(Q, R, P) {
                for (var O = 0, N = I.length; O < N; O = O + 1) {
                    var M = I[O];
                    if (
                        M &&
                        M[this.FN] == P &&
                        M[this.EL] == Q &&
                        M[this.TYPE] == R
                    ) {
                        return O;
                    }
                }
                return -1;
            },
            generateId: function(M) {
                var N = M.id;
                if (!N) {
                    N = "yuievtautoid-" + A;
                    ++A;
                    M.id = N;
                }
                return N;
            },
            _isValidCollection: function(N) {
                try {
                    return (
                        N &&
                        typeof N !== "string" &&
                        N.length &&
                        !N.tagName &&
                        !N.alert &&
                        typeof N[0] !== "undefined"
                    );
                } catch (M) {
                    return false;
                }
            },
            elCache: {},
            getEl: function(M) {
                return typeof M === "string" ? document.getElementById(M) : M;
            },
            clearCache: function() {},
            DOMReadyEvent: new YAHOO.util.CustomEvent("DOMReady", this),
            _load: function(N) {
                if (!H) {
                    H = true;
                    var M = YAHOO.util.Event;
                    M._ready();
                    M._tryPreloadAttach();
                }
            },
            _ready: function(N) {
                var M = YAHOO.util.Event;
                if (!M.DOMReady) {
                    M.DOMReady = true;
                    M.DOMReadyEvent.fire();
                    M._simpleRemove(document, "DOMContentLoaded", M._ready);
                }
            },
            _tryPreloadAttach: function() {
                if (F.length === 0) {
                    C = 0;
                    clearInterval(this._interval);
                    this._interval = null;
                    return;
                }
                if (this.locked) {
                    return;
                }
                if (this.isIE) {
                    if (!this.DOMReady) {
                        this.startInterval();
                        return;
                    }
                }
                this.locked = true;
                var S = !H;
                if (!S) {
                    S = C > 0 && F.length > 0;
                }
                var R = [];
                var T = function(V, W) {
                    var U = V;
                    if (W.override) {
                        if (W.override === true) {
                            U = W.obj;
                        } else {
                            U = W.override;
                        }
                    }
                    W.fn.call(U, W.obj);
                };
                var N,
                    M,
                    Q,
                    P,
                    O = [];
                for (N = 0, M = F.length; N < M; N = N + 1) {
                    Q = F[N];
                    if (Q) {
                        P = this.getEl(Q.id);
                        if (P) {
                            if (Q.checkReady) {
                                if (H || P.nextSibling || !S) {
                                    O.push(Q);
                                    F[N] = null;
                                }
                            } else {
                                T(P, Q);
                                F[N] = null;
                            }
                        } else {
                            R.push(Q);
                        }
                    }
                }
                for (N = 0, M = O.length; N < M; N = N + 1) {
                    Q = O[N];
                    T(this.getEl(Q.id), Q);
                }
                C--;
                if (S) {
                    for (N = F.length - 1; N > -1; N--) {
                        Q = F[N];
                        if (!Q || !Q.id) {
                            F.splice(N, 1);
                        }
                    }
                    this.startInterval();
                } else {
                    clearInterval(this._interval);
                    this._interval = null;
                }
                this.locked = false;
            },
            purgeElement: function(Q, R, T) {
                var O = YAHOO.lang.isString(Q) ? this.getEl(Q) : Q;
                var S = this.getListeners(O, T),
                    P,
                    M;
                if (S) {
                    for (P = S.length - 1; P > -1; P--) {
                        var N = S[P];
                        this._removeListener(O, N.type, N.fn, N.capture);
                    }
                }
                if (R && O && O.childNodes) {
                    for (P = 0, M = O.childNodes.length; P < M; ++P) {
                        this.purgeElement(O.childNodes[P], R, T);
                    }
                }
            },
            getListeners: function(O, M) {
                var R = [],
                    N;
                if (!M) {
                    N = [I, J];
                } else {
                    if (M === "unload") {
                        N = [J];
                    } else {
                        N = [I];
                    }
                }
                var T = YAHOO.lang.isString(O) ? this.getEl(O) : O;
                for (var Q = 0; Q < N.length; Q = Q + 1) {
                    var V = N[Q];
                    if (V) {
                        for (var S = 0, U = V.length; S < U; ++S) {
                            var P = V[S];
                            if (
                                P &&
                                P[this.EL] === T &&
                                (!M || M === P[this.TYPE])
                            ) {
                                R.push({
                                    type: P[this.TYPE],
                                    fn: P[this.FN],
                                    obj: P[this.OBJ],
                                    adjust: P[this.OVERRIDE],
                                    scope: P[this.ADJ_SCOPE],
                                    capture: P[this.CAPTURE],
                                    index: S
                                });
                            }
                        }
                    }
                }
                return R.length ? R : null;
            },
            _unload: function(S) {
                var M = YAHOO.util.Event,
                    P,
                    O,
                    N,
                    R,
                    Q,
                    T = J.slice();
                for (P = 0, R = J.length; P < R; ++P) {
                    N = T[P];
                    if (N) {
                        var U = window;
                        if (N[M.ADJ_SCOPE]) {
                            if (N[M.ADJ_SCOPE] === true) {
                                U = N[M.UNLOAD_OBJ];
                            } else {
                                U = N[M.ADJ_SCOPE];
                            }
                        }
                        N[M.FN].call(
                            U,
                            M.getEvent(S, N[M.EL]),
                            N[M.UNLOAD_OBJ]
                        );
                        T[P] = null;
                        N = null;
                        U = null;
                    }
                }
                J = null;
                if (I) {
                    for (O = I.length - 1; O > -1; O--) {
                        N = I[O];
                        if (N) {
                            M._removeListener(
                                N[M.EL],
                                N[M.TYPE],
                                N[M.FN],
                                N[M.CAPTURE],
                                O
                            );
                        }
                    }
                    N = null;
                }
                G = null;
                M._simpleRemove(window, "unload", M._unload);
            },
            _getScrollLeft: function() {
                return this._getScroll()[1];
            },
            _getScrollTop: function() {
                return this._getScroll()[0];
            },
            _getScroll: function() {
                var M = document.documentElement,
                    N = document.body;
                if (M && (M.scrollTop || M.scrollLeft)) {
                    return [M.scrollTop, M.scrollLeft];
                } else {
                    if (N) {
                        return [N.scrollTop, N.scrollLeft];
                    } else {
                        return [0, 0];
                    }
                }
            },
            regCE: function() {},
            _simpleAdd: (function() {
                if (window.addEventListener) {
                    return function(O, P, N, M) {
                        O.addEventListener(P, N, M);
                    };
                } else {
                    if (window.attachEvent) {
                        return function(O, P, N, M) {
                            O.attachEvent("on" + P, N);
                        };
                    } else {
                        return function() {};
                    }
                }
            })(),
            _simpleRemove: (function() {
                if (window.removeEventListener) {
                    return function(O, P, N, M) {
                        O.removeEventListener(P, N, M);
                    };
                } else {
                    if (window.detachEvent) {
                        return function(N, O, M) {
                            N.detachEvent("on" + O, M);
                        };
                    } else {
                        return function() {};
                    }
                }
            })()
        };
    })();
    (function() {
        var EU = YAHOO.util.Event;
        EU.on = EU.addListener;
        EU.onFocus = EU.addFocusListener;
        EU.onBlur = EU.addBlurListener;
        /* DOMReady: based on work by: Dean Edwards/John Resig/Matthias Miller */
        if (EU.isIE) {
            YAHOO.util.Event.onDOMReady(
                YAHOO.util.Event._tryPreloadAttach,
                YAHOO.util.Event,
                true
            );
            var n = document.createElement("p");
            EU._dri = setInterval(function() {
                try {
                    n.doScroll("left");
                    clearInterval(EU._dri);
                    EU._dri = null;
                    EU._ready();
                    n = null;
                } catch (ex) {}
            }, EU.POLL_INTERVAL);
        } else {
            if (EU.webkit && EU.webkit < 525) {
                EU._dri = setInterval(function() {
                    var rs = document.readyState;
                    if ("loaded" == rs || "complete" == rs) {
                        clearInterval(EU._dri);
                        EU._dri = null;
                        EU._ready();
                    }
                }, EU.POLL_INTERVAL);
            } else {
                EU._simpleAdd(document, "DOMContentLoaded", EU._ready);
            }
        }
        EU._simpleAdd(window, "load", EU._load);
        EU._simpleAdd(window, "unload", EU._unload);
        EU._tryPreloadAttach();
    })();
}
YAHOO.util.EventProvider = function() {};
YAHOO.util.EventProvider.prototype = {
    __yui_events: null,
    __yui_subscribers: null,
    subscribe: function(A, C, F, E) {
        this.__yui_events = this.__yui_events || {};
        var D = this.__yui_events[A];
        if (D) {
            D.subscribe(C, F, E);
        } else {
            this.__yui_subscribers = this.__yui_subscribers || {};
            var B = this.__yui_subscribers;
            if (!B[A]) {
                B[A] = [];
            }
            B[A].push({ fn: C, obj: F, override: E });
        }
    },
    unsubscribe: function(C, E, G) {
        this.__yui_events = this.__yui_events || {};
        var A = this.__yui_events;
        if (C) {
            var F = A[C];
            if (F) {
                return F.unsubscribe(E, G);
            }
        } else {
            var B = true;
            for (var D in A) {
                if (YAHOO.lang.hasOwnProperty(A, D)) {
                    B = B && A[D].unsubscribe(E, G);
                }
            }
            return B;
        }
        return false;
    },
    unsubscribeAll: function(A) {
        return this.unsubscribe(A);
    },
    createEvent: function(G, D) {
        this.__yui_events = this.__yui_events || {};
        var A = D || {};
        var I = this.__yui_events;
        if (I[G]) {} else {
            var H = A.scope || this;
            var E = A.silent;
            var B = new YAHOO.util.CustomEvent(
                G,
                H,
                E,
                YAHOO.util.CustomEvent.FLAT
            );
            I[G] = B;
            if (A.onSubscribeCallback) {
                B.subscribeEvent.subscribe(A.onSubscribeCallback);
            }
            this.__yui_subscribers = this.__yui_subscribers || {};
            var F = this.__yui_subscribers[G];
            if (F) {
                for (var C = 0; C < F.length; ++C) {
                    B.subscribe(F[C].fn, F[C].obj, F[C].override);
                }
            }
        }
        return I[G];
    },
    fireEvent: function(E, D, A, C) {
        this.__yui_events = this.__yui_events || {};
        var G = this.__yui_events[E];
        if (!G) {
            return null;
        }
        var B = [];
        for (var F = 1; F < arguments.length; ++F) {
            B.push(arguments[F]);
        }
        return G.fire.apply(G, B);
    },
    hasEvent: function(A) {
        if (this.__yui_events) {
            if (this.__yui_events[A]) {
                return true;
            }
        }
        return false;
    }
};
YAHOO.util.KeyListener = function(A, F, B, C) {
    if (!A) {} else {
        if (!F) {} else {
            if (!B) {}
        }
    }
    if (!C) {
        C = YAHOO.util.KeyListener.KEYDOWN;
    }
    var D = new YAHOO.util.CustomEvent("keyPressed");
    this.enabledEvent = new YAHOO.util.CustomEvent("enabled");
    this.disabledEvent = new YAHOO.util.CustomEvent("disabled");
    if (typeof A == "string") {
        A = document.getElementById(A);
    }
    if (typeof B == "function") {
        D.subscribe(B);
    } else {
        D.subscribe(B.fn, B.scope, B.correctScope);
    }

    function E(J, I) {
        if (!F.shift) {
            F.shift = false;
        }
        if (!F.alt) {
            F.alt = false;
        }
        if (!F.ctrl) {
            F.ctrl = false;
        }
        if (J.shiftKey == F.shift && J.altKey == F.alt && J.ctrlKey == F.ctrl) {
            var G;
            if (F.keys instanceof Array) {
                for (var H = 0; H < F.keys.length; H++) {
                    G = F.keys[H];
                    if (G == J.charCode) {
                        D.fire(J.charCode, J);
                        break;
                    } else {
                        if (G == J.keyCode) {
                            D.fire(J.keyCode, J);
                            break;
                        }
                    }
                }
            } else {
                G = F.keys;
                if (G == J.charCode) {
                    D.fire(J.charCode, J);
                } else {
                    if (G == J.keyCode) {
                        D.fire(J.keyCode, J);
                    }
                }
            }
        }
    }
    this.enable = function() {
        if (!this.enabled) {
            YAHOO.util.Event.addListener(A, C, E);
            this.enabledEvent.fire(F);
        }
        this.enabled = true;
    };
    this.disable = function() {
        if (this.enabled) {
            YAHOO.util.Event.removeListener(A, C, E);
            this.disabledEvent.fire(F);
        }
        this.enabled = false;
    };
    this.toString = function() {
        return (
            "KeyListener [" +
            F.keys +
            "] " +
            A.tagName +
            (A.id ? "[" + A.id + "]" : "")
        );
    };
};
YAHOO.util.KeyListener.KEYDOWN = "keydown";
YAHOO.util.KeyListener.KEYUP = "keyup";
YAHOO.util.KeyListener.KEY = {
    ALT: 18,
    BACK_SPACE: 8,
    CAPS_LOCK: 20,
    CONTROL: 17,
    DELETE: 46,
    DOWN: 40,
    END: 35,
    ENTER: 13,
    ESCAPE: 27,
    HOME: 36,
    LEFT: 37,
    META: 224,
    NUM_LOCK: 144,
    PAGE_DOWN: 34,
    PAGE_UP: 33,
    PAUSE: 19,
    PRINTSCREEN: 44,
    RIGHT: 39,
    SCROLL_LOCK: 145,
    SHIFT: 16,
    SPACE: 32,
    TAB: 9,
    UP: 38
};
YAHOO.register("event", YAHOO.util.Event, { version: "2.6.0", build: "1321" });
YAHOO.register("yahoo-dom-event", YAHOO, { version: "2.6.0", build: "1321" });
/*
        Copyright (c) 2008, Yahoo! Inc. All rights reserved.
        Code licensed under the BSD License:
        http://developer.yahoo.net/yui/license.txt
        version: 2.6.0
        */
(function() {
    var B = YAHOO.util;
    var A = function(D, C, E, F) {
        if (!D) {}
        this.init(D, C, E, F);
    };
    A.NAME = "Anim";
    A.prototype = {
        toString: function() {
            var C = this.getEl() || {};
            var D = C.id || C.tagName;
            return this.constructor.NAME + ": " + D;
        },
        patterns: {
            noNegatives: /width|height|opacity|padding/i,
            offsetAttribute: /^((width|height)|(top|left))$/,
            defaultUnit: /width|height|top$|bottom$|left$|right$/i,
            offsetUnit: /\d+(em|%|en|ex|pt|in|cm|mm|pc)$/i
        },
        doMethod: function(C, E, D) {
            return this.method(this.currentFrame, E, D - E, this.totalFrames);
        },
        setAttribute: function(C, E, D) {
            if (this.patterns.noNegatives.test(C)) {
                E = E > 0 ? E : 0;
            }
            B.Dom.setStyle(this.getEl(), C, E + D);
        },
        getAttribute: function(C) {
            var E = this.getEl();
            var G = B.Dom.getStyle(E, C);
            if (G !== "auto" && !this.patterns.offsetUnit.test(G)) {
                return parseFloat(G);
            }
            var D = this.patterns.offsetAttribute.exec(C) || [];
            var H = !!D[3];
            var F = !!D[2];
            if (F || (B.Dom.getStyle(E, "position") == "absolute" && H)) {
                G = E["offset" + D[0].charAt(0).toUpperCase() + D[0].substr(1)];
            } else {
                G = 0;
            }
            return G;
        },
        getDefaultUnit: function(C) {
            if (this.patterns.defaultUnit.test(C)) {
                return "px";
            }
            return "";
        },
        setRuntimeAttribute: function(D) {
            var I;
            var E;
            var F = this.attributes;
            this.runtimeAttributes[D] = {};
            var H = function(J) {
                return typeof J !== "undefined";
            };
            if (!H(F[D]["to"]) && !H(F[D]["by"])) {
                return false;
            }
            I = H(F[D]["from"]) ? F[D]["from"] : this.getAttribute(D);
            if (H(F[D]["to"])) {
                E = F[D]["to"];
            } else {
                if (H(F[D]["by"])) {
                    if (I.constructor == Array) {
                        E = [];
                        for (var G = 0, C = I.length; G < C; ++G) {
                            E[G] = I[G] + F[D]["by"][G] * 1;
                        }
                    } else {
                        E = I + F[D]["by"] * 1;
                    }
                }
            }
            this.runtimeAttributes[D].start = I;
            this.runtimeAttributes[D].end = E;
            this.runtimeAttributes[D].unit = H(F[D].unit) ?
                F[D]["unit"] :
                this.getDefaultUnit(D);
            return true;
        },
        init: function(E, J, I, C) {
            var D = false;
            var F = null;
            var H = 0;
            E = B.Dom.get(E);
            this.attributes = J || {};
            this.duration = !YAHOO.lang.isUndefined(I) ? I : 1;
            this.method = C || B.Easing.easeNone;
            this.useSeconds = true;
            this.currentFrame = 0;
            this.totalFrames = B.AnimMgr.fps;
            this.setEl = function(M) {
                E = B.Dom.get(M);
            };
            this.getEl = function() {
                return E;
            };
            this.isAnimated = function() {
                return D;
            };
            this.getStartTime = function() {
                return F;
            };
            this.runtimeAttributes = {};
            this.animate = function() {
                if (this.isAnimated()) {
                    return false;
                }
                this.currentFrame = 0;
                this.totalFrames = this.useSeconds ?
                    Math.ceil(B.AnimMgr.fps * this.duration) :
                    this.duration;
                if (this.duration === 0 && this.useSeconds) {
                    this.totalFrames = 1;
                }
                B.AnimMgr.registerElement(this);
                return true;
            };
            this.stop = function(M) {
                if (!this.isAnimated()) {
                    return false;
                }
                if (M) {
                    this.currentFrame = this.totalFrames;
                    this._onTween.fire();
                }
                B.AnimMgr.stop(this);
            };
            var L = function() {
                this.onStart.fire();
                this.runtimeAttributes = {};
                for (var M in this.attributes) {
                    this.setRuntimeAttribute(M);
                }
                D = true;
                H = 0;
                F = new Date();
            };
            var K = function() {
                var O = {
                    duration: new Date() - this.getStartTime(),
                    currentFrame: this.currentFrame
                };
                O.toString = function() {
                    return (
                        "duration: " +
                        O.duration +
                        ", currentFrame: " +
                        O.currentFrame
                    );
                };
                this.onTween.fire(O);
                var N = this.runtimeAttributes;
                for (var M in N) {
                    this.setAttribute(
                        M,
                        this.doMethod(M, N[M].start, N[M].end),
                        N[M].unit
                    );
                }
                H += 1;
            };
            var G = function() {
                var M = (new Date() - F) / 1000;
                var N = { duration: M, frames: H, fps: H / M };
                N.toString = function() {
                    return (
                        "duration: " +
                        N.duration +
                        ", frames: " +
                        N.frames +
                        ", fps: " +
                        N.fps
                    );
                };
                D = false;
                H = 0;
                this.onComplete.fire(N);
            };
            this._onStart = new B.CustomEvent("_start", this, true);
            this.onStart = new B.CustomEvent("start", this);
            this.onTween = new B.CustomEvent("tween", this);
            this._onTween = new B.CustomEvent("_tween", this, true);
            this.onComplete = new B.CustomEvent("complete", this);
            this._onComplete = new B.CustomEvent("_complete", this, true);
            this._onStart.subscribe(L);
            this._onTween.subscribe(K);
            this._onComplete.subscribe(G);
        }
    };
    B.Anim = A;
})();
YAHOO.util.AnimMgr = new(function() {
    var C = null;
    var B = [];
    var A = 0;
    this.fps = 1000;
    this.delay = 1;
    this.registerElement = function(F) {
        B[B.length] = F;
        A += 1;
        F._onStart.fire();
        this.start();
    };
    this.unRegister = function(G, F) {
        F = F || E(G);
        if (!G.isAnimated() || F == -1) {
            return false;
        }
        G._onComplete.fire();
        B.splice(F, 1);
        A -= 1;
        if (A <= 0) {
            this.stop();
        }
        return true;
    };
    this.start = function() {
        if (C === null) {
            C = setInterval(this.run, this.delay);
        }
    };
    this.stop = function(H) {
        if (!H) {
            clearInterval(C);
            for (var G = 0, F = B.length; G < F; ++G) {
                this.unRegister(B[0], 0);
            }
            B = [];
            C = null;
            A = 0;
        } else {
            this.unRegister(H);
        }
    };
    this.run = function() {
        for (var H = 0, F = B.length; H < F; ++H) {
            var G = B[H];
            if (!G || !G.isAnimated()) {
                continue;
            }
            if (G.currentFrame < G.totalFrames || G.totalFrames === null) {
                G.currentFrame += 1;
                if (G.useSeconds) {
                    D(G);
                }
                G._onTween.fire();
            } else {
                YAHOO.util.AnimMgr.stop(G, H);
            }
        }
    };
    var E = function(H) {
        for (var G = 0, F = B.length; G < F; ++G) {
            if (B[G] == H) {
                return G;
            }
        }
        return -1;
    };
    var D = function(G) {
        var J = G.totalFrames;
        var I = G.currentFrame;
        var H = (G.currentFrame * G.duration * 1000) / G.totalFrames;
        var F = new Date() - G.getStartTime();
        var K = 0;
        if (F < G.duration * 1000) {
            K = Math.round((F / H - 1) * G.currentFrame);
        } else {
            K = J - (I + 1);
        }
        if (K > 0 && isFinite(K)) {
            if (G.currentFrame + K >= J) {
                K = J - (I + 1);
            }
            G.currentFrame += K;
        }
    };
})();
YAHOO.util.Bezier = new(function() {
    this.getPosition = function(E, D) {
        var F = E.length;
        var C = [];
        for (var B = 0; B < F; ++B) {
            C[B] = [E[B][0], E[B][1]];
        }
        for (var A = 1; A < F; ++A) {
            for (B = 0; B < F - A; ++B) {
                C[B][0] = (1 - D) * C[B][0] + D * C[parseInt(B + 1, 10)][0];
                C[B][1] = (1 - D) * C[B][1] + D * C[parseInt(B + 1, 10)][1];
            }
        }
        return [C[0][0], C[0][1]];
    };
})();
(function() {
    var A = function(F, E, G, H) {
        A.superclass.constructor.call(this, F, E, G, H);
    };
    A.NAME = "ColorAnim";
    A.DEFAULT_BGCOLOR = "#fff";
    var C = YAHOO.util;
    YAHOO.extend(A, C.Anim);
    var D = A.superclass;
    var B = A.prototype;
    B.patterns.color = /color$/i;
    B.patterns.rgb = /^rgb\(([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\)$/i;
    B.patterns.hex = /^#?([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})$/i;
    B.patterns.hex3 = /^#?([0-9A-F]{1})([0-9A-F]{1})([0-9A-F]{1})$/i;
    B.patterns.transparent = /^transparent|rgba\(0, 0, 0, 0\)$/;
    B.parseColor = function(E) {
        if (E.length == 3) {
            return E;
        }
        var F = this.patterns.hex.exec(E);
        if (F && F.length == 4) {
            return [parseInt(F[1], 16), parseInt(F[2], 16), parseInt(F[3], 16)];
        }
        F = this.patterns.rgb.exec(E);
        if (F && F.length == 4) {
            return [parseInt(F[1], 10), parseInt(F[2], 10), parseInt(F[3], 10)];
        }
        F = this.patterns.hex3.exec(E);
        if (F && F.length == 4) {
            return [
                parseInt(F[1] + F[1], 16),
                parseInt(F[2] + F[2], 16),
                parseInt(F[3] + F[3], 16)
            ];
        }
        return null;
    };
    B.getAttribute = function(E) {
        var G = this.getEl();
        if (this.patterns.color.test(E)) {
            var I = YAHOO.util.Dom.getStyle(G, E);
            var H = this;
            if (this.patterns.transparent.test(I)) {
                var F = YAHOO.util.Dom.getAncestorBy(G, function(J) {
                    return !H.patterns.transparent.test(I);
                });
                if (F) {
                    I = C.Dom.getStyle(F, E);
                } else {
                    I = A.DEFAULT_BGCOLOR;
                }
            }
        } else {
            I = D.getAttribute.call(this, E);
        }
        return I;
    };
    B.doMethod = function(F, J, G) {
        var I;
        if (this.patterns.color.test(F)) {
            I = [];
            for (var H = 0, E = J.length; H < E; ++H) {
                I[H] = D.doMethod.call(this, F, J[H], G[H]);
            }
            I =
                "rgb(" +
                Math.floor(I[0]) +
                "," +
                Math.floor(I[1]) +
                "," +
                Math.floor(I[2]) +
                ")";
        } else {
            I = D.doMethod.call(this, F, J, G);
        }
        return I;
    };
    B.setRuntimeAttribute = function(F) {
        D.setRuntimeAttribute.call(this, F);
        if (this.patterns.color.test(F)) {
            var H = this.attributes;
            var J = this.parseColor(this.runtimeAttributes[F].start);
            var G = this.parseColor(this.runtimeAttributes[F].end);
            if (
                typeof H[F]["to"] === "undefined" &&
                typeof H[F]["by"] !== "undefined"
            ) {
                G = this.parseColor(H[F].by);
                for (var I = 0, E = J.length; I < E; ++I) {
                    G[I] = J[I] + G[I];
                }
            }
            this.runtimeAttributes[F].start = J;
            this.runtimeAttributes[F].end = G;
        }
    };
    C.ColorAnim = A;
})();
/*
        TERMS OF USE - EASING EQUATIONS
        Open source under the BSD License.
        Copyright 2001 Robert Penner All rights reserved.

        Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

        * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
        * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
        * Neither the name of the author nor the names of contributors may be used to endorse or promote products derived from this software without specific prior written permission.

        THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
        */
YAHOO.util.Easing = {
    easeNone: function(B, A, D, C) {
        return (D * B) / C + A;
    },
    easeIn: function(B, A, D, C) {
        return D * (B /= C) * B + A;
    },
    easeOut: function(B, A, D, C) {
        return -D * (B /= C) * (B - 2) + A;
    },
    easeBoth: function(B, A, D, C) {
        if ((B /= C / 2) < 1) {
            return (D / 2) * B * B + A;
        }
        return (-D / 2) * (--B * (B - 2) - 1) + A;
    },
    easeInStrong: function(B, A, D, C) {
        return D * (B /= C) * B * B * B + A;
    },
    easeOutStrong: function(B, A, D, C) {
        return -D * ((B = B / C - 1) * B * B * B - 1) + A;
    },
    easeBothStrong: function(B, A, D, C) {
        if ((B /= C / 2) < 1) {
            return (D / 2) * B * B * B * B + A;
        }
        return (-D / 2) * ((B -= 2) * B * B * B - 2) + A;
    },
    elasticIn: function(C, A, G, F, B, E) {
        if (C == 0) {
            return A;
        }
        if ((C /= F) == 1) {
            return A + G;
        }
        if (!E) {
            E = F * 0.3;
        }
        if (!B || B < Math.abs(G)) {
            B = G;
            var D = E / 4;
        } else {
            var D = (E / (2 * Math.PI)) * Math.asin(G / B);
        }
        return (-(
            B *
            Math.pow(2, 10 * (C -= 1)) *
            Math.sin(((C * F - D) * (2 * Math.PI)) / E)
        ) + A);
    },
    elasticOut: function(C, A, G, F, B, E) {
        if (C == 0) {
            return A;
        }
        if ((C /= F) == 1) {
            return A + G;
        }
        if (!E) {
            E = F * 0.3;
        }
        if (!B || B < Math.abs(G)) {
            B = G;
            var D = E / 4;
        } else {
            var D = (E / (2 * Math.PI)) * Math.asin(G / B);
        }
        return (
            B *
            Math.pow(2, -10 * C) *
            Math.sin(((C * F - D) * (2 * Math.PI)) / E) +
            G +
            A
        );
    },
    elasticBoth: function(C, A, G, F, B, E) {
        if (C == 0) {
            return A;
        }
        if ((C /= F / 2) == 2) {
            return A + G;
        }
        if (!E) {
            E = F * (0.3 * 1.5);
        }
        if (!B || B < Math.abs(G)) {
            B = G;
            var D = E / 4;
        } else {
            var D = (E / (2 * Math.PI)) * Math.asin(G / B);
        }
        if (C < 1) {
            return (-0.5 *
                (B *
                    Math.pow(2, 10 * (C -= 1)) *
                    Math.sin(((C * F - D) * (2 * Math.PI)) / E)) +
                A
            );
        }
        return (
            B *
            Math.pow(2, -10 * (C -= 1)) *
            Math.sin(((C * F - D) * (2 * Math.PI)) / E) *
            0.5 +
            G +
            A
        );
    },
    backIn: function(B, A, E, D, C) {
        if (typeof C == "undefined") {
            C = 1.70158;
        }
        return E * (B /= D) * B * ((C + 1) * B - C) + A;
    },
    backOut: function(B, A, E, D, C) {
        if (typeof C == "undefined") {
            C = 1.70158;
        }
        return E * ((B = B / D - 1) * B * ((C + 1) * B + C) + 1) + A;
    },
    backBoth: function(B, A, E, D, C) {
        if (typeof C == "undefined") {
            C = 1.70158;
        }
        if ((B /= D / 2) < 1) {
            return (E / 2) * (B * B * (((C *= 1.525) + 1) * B - C)) + A;
        }
        return (E / 2) * ((B -= 2) * B * (((C *= 1.525) + 1) * B + C) + 2) + A;
    },
    bounceIn: function(B, A, D, C) {
        return D - YAHOO.util.Easing.bounceOut(C - B, 0, D, C) + A;
    },
    bounceOut: function(B, A, D, C) {
        if ((B /= C) < 1 / 2.75) {
            return D * (7.5625 * B * B) + A;
        } else {
            if (B < 2 / 2.75) {
                return D * (7.5625 * (B -= 1.5 / 2.75) * B + 0.75) + A;
            } else {
                if (B < 2.5 / 2.75) {
                    return D * (7.5625 * (B -= 2.25 / 2.75) * B + 0.9375) + A;
                }
            }
        }
        return D * (7.5625 * (B -= 2.625 / 2.75) * B + 0.984375) + A;
    },
    bounceBoth: function(B, A, D, C) {
        if (B < C / 2) {
            return YAHOO.util.Easing.bounceIn(B * 2, 0, D, C) * 0.5 + A;
        }
        return (
            YAHOO.util.Easing.bounceOut(B * 2 - C, 0, D, C) * 0.5 + D * 0.5 + A
        );
    }
};
(function() {
    var A = function(H, G, I, J) {
        if (H) {
            A.superclass.constructor.call(this, H, G, I, J);
        }
    };
    A.NAME = "Motion";
    var E = YAHOO.util;
    YAHOO.extend(A, E.ColorAnim);
    var F = A.superclass;
    var C = A.prototype;
    C.patterns.points = /^points$/i;
    C.setAttribute = function(G, I, H) {
        if (this.patterns.points.test(G)) {
            H = H || "px";
            F.setAttribute.call(this, "left", I[0], H);
            F.setAttribute.call(this, "top", I[1], H);
        } else {
            F.setAttribute.call(this, G, I, H);
        }
    };
    C.getAttribute = function(G) {
        if (this.patterns.points.test(G)) {
            var H = [
                F.getAttribute.call(this, "left"),
                F.getAttribute.call(this, "top")
            ];
        } else {
            H = F.getAttribute.call(this, G);
        }
        return H;
    };
    C.doMethod = function(G, K, H) {
        var J = null;
        if (this.patterns.points.test(G)) {
            var I =
                this.method(this.currentFrame, 0, 100, this.totalFrames) / 100;
            J = E.Bezier.getPosition(this.runtimeAttributes[G], I);
        } else {
            J = F.doMethod.call(this, G, K, H);
        }
        return J;
    };
    C.setRuntimeAttribute = function(P) {
        if (this.patterns.points.test(P)) {
            var H = this.getEl();
            var J = this.attributes;
            var G;
            var L = J["points"]["control"] || [];
            var I;
            var M, O;
            if (L.length > 0 && !(L[0] instanceof Array)) {
                L = [L];
            } else {
                var K = [];
                for (M = 0, O = L.length; M < O; ++M) {
                    K[M] = L[M];
                }
                L = K;
            }
            if (E.Dom.getStyle(H, "position") == "static") {
                E.Dom.setStyle(H, "position", "relative");
            }
            if (D(J["points"]["from"])) {
                E.Dom.setXY(H, J["points"]["from"]);
            } else {
                E.Dom.setXY(H, E.Dom.getXY(H));
            }
            G = this.getAttribute("points");
            if (D(J["points"]["to"])) {
                I = B.call(this, J["points"]["to"], G);
                var N = E.Dom.getXY(this.getEl());
                for (M = 0, O = L.length; M < O; ++M) {
                    L[M] = B.call(this, L[M], G);
                }
            } else {
                if (D(J["points"]["by"])) {
                    I = [
                        G[0] + J["points"]["by"][0],
                        G[1] + J["points"]["by"][1]
                    ];
                    for (M = 0, O = L.length; M < O; ++M) {
                        L[M] = [G[0] + L[M][0], G[1] + L[M][1]];
                    }
                }
            }
            this.runtimeAttributes[P] = [G];
            if (L.length > 0) {
                this.runtimeAttributes[P] = this.runtimeAttributes[P].concat(L);
            }
            this.runtimeAttributes[P][this.runtimeAttributes[P].length] = I;
        } else {
            F.setRuntimeAttribute.call(this, P);
        }
    };
    var B = function(G, I) {
        var H = E.Dom.getXY(this.getEl());
        G = [G[0] - H[0] + I[0], G[1] - H[1] + I[1]];
        return G;
    };
    var D = function(G) {
        return typeof G !== "undefined";
    };
    E.Motion = A;
})();
(function() {
    var D = function(F, E, G, H) {
        if (F) {
            D.superclass.constructor.call(this, F, E, G, H);
        }
    };
    D.NAME = "Scroll";
    var B = YAHOO.util;
    YAHOO.extend(D, B.ColorAnim);
    var C = D.superclass;
    var A = D.prototype;
    A.doMethod = function(E, H, F) {
        var G = null;
        if (E == "scroll") {
            G = [
                this.method(
                    this.currentFrame,
                    H[0],
                    F[0] - H[0],
                    this.totalFrames
                ),
                this.method(
                    this.currentFrame,
                    H[1],
                    F[1] - H[1],
                    this.totalFrames
                )
            ];
        } else {
            G = C.doMethod.call(this, E, H, F);
        }
        return G;
    };
    A.getAttribute = function(E) {
        var G = null;
        var F = this.getEl();
        if (E == "scroll") {
            G = [F.scrollLeft, F.scrollTop];
        } else {
            G = C.getAttribute.call(this, E);
        }
        return G;
    };
    A.setAttribute = function(E, H, G) {
        var F = this.getEl();
        if (E == "scroll") {
            F.scrollLeft = H[0];
            F.scrollTop = H[1];
        } else {
            C.setAttribute.call(this, E, H, G);
        }
    };
    B.Scroll = D;
})();
YAHOO.register("animation", YAHOO.util.Anim, {
    version: "2.6.0",
    build: "1321"
});


/** @license
 * DHTML Snowstorm! JavaScript-based snow for web pages
 * Making it snow on the internets since 2003. You're welcome.
 * -----------------------------------------------------------
 * Version 1.44.20131208 (Previous rev: 1.44.20131125)
 * Copyright (c) 2007, Scott Schiller. All rights reserved.
 * Code provided under the BSD License
 * http://schillmania.com/projects/snowstorm/license.txt
 */

/*jslint nomen: true, plusplus: true, sloppy: true, vars: true, white: true */
/*global window, document, navigator, clearInterval, setInterval */

var snowStorm = (function(window, document) {
    // --- common properties ---

    this.autoStart = true; // Whether the snow should start automatically or not.
    this.excludeMobile = true; // Snow is likely to be bad news for mobile phones' CPUs (and batteries.) Enable at your own risk.
    this.flakesMax = 128; // Limit total amount of snow made (falling + sticking)
    this.flakesMaxActive = 64; // Limit amount of snow falling at once (less = lower CPU use)
    this.animationInterval = 50; // Theoretical "miliseconds per frame" measurement. 20 = fast + smooth, but high CPU use. 50 = more conservative, but slower
    this.useGPU = true; // Enable transform-based hardware acceleration, reduce CPU load.
    this.className = null; // CSS class name for further customization on snow elements
    this.excludeMobile = true; // Snow is likely to be bad news for mobile phones' CPUs (and batteries.) By default, be nice.
    this.flakeBottom = null; // Integer for Y axis snow limit, 0 or null for "full-screen" snow effect
    this.followMouse = false; // Snow movement can respond to the user's mouse
    this.snowColor = "#fff"; // Don't eat (or use?) yellow snow.
    this.snowCharacter = "&bull;"; // &bull; = bullet, &middot; is square on some systems etc.
    this.snowStick = true; // Whether or not snow should "stick" at the bottom. When off, will never collect.
    this.targetElement = null; // element which snow will be appended to (null = document.body) - can be an element ID eg. 'myDiv', or a DOM node reference
    this.useMeltEffect = true; // When recycling fallen snow (or rarely, when falling), have it "melt" and fade out if browser supports it
    this.useTwinkleEffect = false; // Allow snow to randomly "flicker" in and out of view while falling
    this.usePositionFixed = false; // true = snow does not shift vertically when scrolling. May increase CPU load, disabled by default - if enabled, used only where supported
    this.usePixelPosition = false; // Whether to use pixel values for snow top/left vs. percentages. Auto-enabled if body is position:relative or targetElement is specified.

    // --- less-used bits ---

    this.freezeOnBlur = true; // Only snow when the window is in focus (foreground.) Saves CPU.
    this.flakeLeftOffset = 0; // Left margin/gutter space on edge of container (eg. browser window.) Bump up these values if seeing horizontal scrollbars.
    this.flakeRightOffset = 0; // Right margin/gutter space on edge of container
    this.flakeWidth = 8; // Max pixel width reserved for snow element
    this.flakeHeight = 8; // Max pixel height reserved for snow element
    this.vMaxX = 5; // Maximum X velocity range for snow
    this.vMaxY = 4; // Maximum Y velocity range for snow
    this.zIndex = 0; // CSS stacking order applied to each snowflake

    // --- "No user-serviceable parts inside" past this point, yadda yadda ---

    var storm = this,
        features,
        // UA sniffing and backCompat rendering mode checks for fixed position, etc.
        isIE = navigator.userAgent.match(/msie/i),
        isIE6 = navigator.userAgent.match(/msie 6/i),
        isMobile = navigator.userAgent.match(/mobile|opera m(ob|in)/i),
        isBackCompatIE = isIE && document.compatMode === "BackCompat",
        noFixed = isBackCompatIE || isIE6,
        screenX = null,
        screenX2 = null,
        screenY = null,
        scrollY = null,
        docHeight = null,
        vRndX = null,
        vRndY = null,
        windOffset = 1,
        windMultiplier = 2,
        flakeTypes = 6,
        fixedForEverything = false,
        targetElementIsRelative = false,
        opacitySupported = (function() {
            try {
                document.createElement("div").style.opacity = "0.5";
            } catch (e) {
                return false;
            }
            return true;
        })(),
        didInit = false,
        docFrag = document.createDocumentFragment();

    features = (function() {
        var getAnimationFrame;

        /**
         * hat tip: paul irish
         * http://paulirish.com/2011/requestanimationframe-for-smart-animating/
         * https://gist.github.com/838785
         */

        function timeoutShim(callback) {
            window.setTimeout(callback, 1000 / (storm.animationInterval || 20));
        }

        var _animationFrame =
            window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.oRequestAnimationFrame ||
            window.msRequestAnimationFrame ||
            timeoutShim;

        // apply to window, avoid "illegal invocation" errors in Chrome
        getAnimationFrame = _animationFrame ?

            function() {
                return _animationFrame.apply(window, arguments);
            } :
            null;

        var testDiv;

        testDiv = document.createElement("div");

        function has(prop) {
            // test for feature support
            var result = testDiv.style[prop];
            return result !== undefined ? prop : null;
        }

        // note local scope.
        var localFeatures = {
            transform: {
                ie: has("-ms-transform"),
                moz: has("MozTransform"),
                opera: has("OTransform"),
                webkit: has("webkitTransform"),
                w3: has("transform"),
                prop: null // the normalized property value
            },

            getAnimationFrame: getAnimationFrame
        };

        localFeatures.transform.prop =
            localFeatures.transform.w3 ||
            localFeatures.transform.moz ||
            localFeatures.transform.webkit ||
            localFeatures.transform.ie ||
            localFeatures.transform.opera;

        testDiv = null;

        return localFeatures;
    })();

    this.timer = null;
    this.flakes = [];
    this.disabled = false;
    this.active = false;
    this.meltFrameCount = 20;
    this.meltFrames = [];

    this.setXY = function(o, x, y) {
        if (!o) {
            return false;
        }

        if (storm.usePixelPosition || targetElementIsRelative) {
            o.style.left = x - storm.flakeWidth + "px";
            o.style.top = y - storm.flakeHeight + "px";
        } else if (noFixed) {
            o.style.right = 100 - (x / screenX) * 100 + "%";
            // avoid creating vertical scrollbars
            o.style.top = Math.min(y, docHeight - storm.flakeHeight) + "px";
        } else {
            if (!storm.flakeBottom) {
                // if not using a fixed bottom coordinate...
                o.style.right = 100 - (x / screenX) * 100 + "%";
                o.style.bottom = 100 - (y / screenY) * 100 + "%";
            } else {
                // absolute top.
                o.style.right = 100 - (x / screenX) * 100 + "%";
                o.style.top = Math.min(y, docHeight - storm.flakeHeight) + "px";
            }
        }
    };

    this.events = (function() {
        var old = !window.addEventListener && window.attachEvent,
            slice = Array.prototype.slice,
            evt = {
                add: old ? "attachEvent" : "addEventListener",
                remove: old ? "detachEvent" : "removeEventListener"
            };

        function getArgs(oArgs) {
            var args = slice.call(oArgs),
                len = args.length;
            if (old) {
                args[1] = "on" + args[1]; // prefix
                if (len > 3) {
                    args.pop(); // no capture
                }
            } else if (len === 3) {
                args.push(false);
            }
            return args;
        }

        function apply(args, sType) {
            var element = args.shift(),
                method = [evt[sType]];
            if (old) {
                element[method](args[0], args[1]);
            } else {
                element[method].apply(element, args);
            }
        }

        function addEvent() {
            apply(getArgs(arguments), "add");
        }

        function removeEvent() {
            apply(getArgs(arguments), "remove");
        }

        return {
            add: addEvent,
            remove: removeEvent
        };
    })();

    function rnd(n, min) {
        if (isNaN(min)) {
            min = 0;
        }
        return Math.random() * n + min;
    }

    function plusMinus(n) {
        return parseInt(rnd(2), 10) === 1 ? n * -1 : n;
    }

    this.randomizeWind = function() {
        var i;
        vRndX = plusMinus(rnd(storm.vMaxX, 0.2));
        vRndY = rnd(storm.vMaxY, 0.2);
        if (this.flakes) {
            for (i = 0; i < this.flakes.length; i++) {
                if (this.flakes[i].active) {
                    this.flakes[i].setVelocities();
                }
            }
        }
    };

    this.scrollHandler = function() {
        var i;
        // "attach" snowflakes to bottom of window if no absolute bottom value was given
        scrollY = storm.flakeBottom ?
            0 :
            parseInt(
                window.scrollY ||
                document.documentElement.scrollTop ||
                (noFixed ? document.body.scrollTop : 0),
                10
            );
        if (isNaN(scrollY)) {
            scrollY = 0; // Netscape 6 scroll fix
        }
        if (!fixedForEverything && !storm.flakeBottom && storm.flakes) {
            for (i = 0; i < storm.flakes.length; i++) {
                if (storm.flakes[i].active === 0) {
                    storm.flakes[i].stick();
                }
            }
        }
    };

    this.resizeHandler = function() {
        if (window.innerWidth || window.innerHeight) {
            screenX = window.innerWidth - 16 - storm.flakeRightOffset;
            screenY = storm.flakeBottom || window.innerHeight;
        } else {
            screenX =
                (document.documentElement.clientWidth ||
                    document.body.clientWidth ||
                    document.body.scrollWidth) -
                (!isIE ? 8 : 0) -
                storm.flakeRightOffset;
            screenY =
                storm.flakeBottom ||
                document.documentElement.clientHeight ||
                document.body.clientHeight ||
                document.body.scrollHeight;
        }
        docHeight = document.body.offsetHeight;
        screenX2 = parseInt(screenX / 2, 10);
    };

    this.resizeHandlerAlt = function() {
        screenX = storm.targetElement.offsetWidth - storm.flakeRightOffset;
        screenY = storm.flakeBottom || storm.targetElement.offsetHeight;
        screenX2 = parseInt(screenX / 2, 10);
        docHeight = document.body.offsetHeight;
    };

    this.freeze = function() {
        // pause animation
        if (!storm.disabled) {
            storm.disabled = 1;
        } else {
            return false;
        }
        storm.timer = null;
    };

    this.resume = function() {
        if (storm.disabled) {
            storm.disabled = 0;
        } else {
            return false;
        }
        storm.timerInit();
    };

    this.toggleSnow = function() {
        if (!storm.flakes.length) {
            // first run
            storm.start();
        } else {
            storm.active = !storm.active;
            if (storm.active) {
                storm.show();
                storm.resume();
            } else {
                storm.stop();
                storm.freeze();
            }
        }
    };

    this.stop = function() {
        var i;
        this.freeze();
        for (i = 0; i < this.flakes.length; i++) {
            this.flakes[i].o.style.display = "none";
        }
        storm.events.remove(window, "scroll", storm.scrollHandler);
        storm.events.remove(window, "resize", storm.resizeHandler);
        if (storm.freezeOnBlur) {
            if (isIE) {
                storm.events.remove(document, "focusout", storm.freeze);
                storm.events.remove(document, "focusin", storm.resume);
            } else {
                storm.events.remove(window, "blur", storm.freeze);
                storm.events.remove(window, "focus", storm.resume);
            }
        }
    };

    this.show = function() {
        var i;
        for (i = 0; i < this.flakes.length; i++) {
            this.flakes[i].o.style.display = "block";
        }
    };

    this.SnowFlake = function(type, x, y) {
        var s = this;
        this.type = type;
        this.x = x || parseInt(rnd(screenX - 20), 10);
        this.y = !isNaN(y) ? y : -rnd(screenY) - 12;
        this.vX = null;
        this.vY = null;
        this.vAmpTypes = [1, 1.2, 1.4, 1.6, 1.8]; // "amplification" for vX/vY (based on flake size/type)
        this.vAmp = this.vAmpTypes[this.type] || 1;
        this.melting = false;
        this.meltFrameCount = storm.meltFrameCount;
        this.meltFrames = storm.meltFrames;
        this.meltFrame = 0;
        this.twinkleFrame = 0;
        this.active = 1;
        this.fontSize = 10 + (this.type / 5) * 10;
        this.o = document.createElement("div");
        this.o.innerHTML = storm.snowCharacter;
        if (storm.className) {
            this.o.setAttribute("class", storm.className);
        }
        this.o.style.color = storm.snowColor;
        this.o.style.position = fixedForEverything ? "fixed" : "absolute";
        if (storm.useGPU && features.transform.prop) {
            // GPU-accelerated snow.
            this.o.style[features.transform.prop] =
                "translate3d(0px, 0px, 0px)";
        }
        this.o.style.width = storm.flakeWidth + "px";
        this.o.style.height = storm.flakeHeight + "px";
        this.o.style.fontFamily = "arial,verdana";
        this.o.style.cursor = "default";
        this.o.style.overflow = "hidden";
        this.o.style.fontWeight = "normal";
        this.o.style.zIndex = storm.zIndex;
        docFrag.appendChild(this.o);

        this.refresh = function() {
            if (isNaN(s.x) || isNaN(s.y)) {
                // safety check
                return false;
            }
            storm.setXY(s.o, s.x, s.y);
        };

        this.stick = function() {
            if (
                noFixed ||
                (storm.targetElement !== document.documentElement &&
                    storm.targetElement !== document.body)
            ) {
                s.o.style.top = screenY + scrollY - storm.flakeHeight + "px";
            } else if (storm.flakeBottom) {
                s.o.style.top = storm.flakeBottom + "px";
            } else {
                s.o.style.display = "none";
                s.o.style.bottom = "0%";
                s.o.style.position = "fixed";
                s.o.style.display = "block";
            }
        };

        this.vCheck = function() {
            if (s.vX >= 0 && s.vX < 0.2) {
                s.vX = 0.2;
            } else if (s.vX < 0 && s.vX > -0.2) {
                s.vX = -0.2;
            }
            if (s.vY >= 0 && s.vY < 0.2) {
                s.vY = 0.2;
            }
        };

        this.move = function() {
            var vX = s.vX * windOffset,
                yDiff;
            s.x += vX;
            s.y += s.vY * s.vAmp;
            if (s.x >= screenX || screenX - s.x < storm.flakeWidth) {
                // X-axis scroll check
                s.x = 0;
            } else if (
                vX < 0 &&
                s.x - storm.flakeLeftOffset < -storm.flakeWidth
            ) {
                s.x = screenX - storm.flakeWidth - 1; // flakeWidth;
            }
            s.refresh();
            yDiff = screenY + scrollY - s.y + storm.flakeHeight;
            if (yDiff < storm.flakeHeight) {
                s.active = 0;
                if (storm.snowStick) {
                    s.stick();
                } else {
                    s.recycle();
                }
            } else {
                if (
                    storm.useMeltEffect &&
                    s.active &&
                    s.type < 3 &&
                    !s.melting &&
                    Math.random() > 0.998
                ) {
                    // ~1/1000 chance of melting mid-air, with each frame
                    s.melting = true;
                    s.melt();
                    // only incrementally melt one frame
                    // s.melting = false;
                }
                if (storm.useTwinkleEffect) {
                    if (s.twinkleFrame < 0) {
                        if (Math.random() > 0.97) {
                            s.twinkleFrame = parseInt(Math.random() * 8, 10);
                        }
                    } else {
                        s.twinkleFrame--;
                        if (!opacitySupported) {
                            s.o.style.visibility =
                                s.twinkleFrame && s.twinkleFrame % 2 === 0 ?
                                "hidden" :
                                "visible";
                        } else {
                            s.o.style.opacity =
                                s.twinkleFrame && s.twinkleFrame % 2 === 0 ?
                                0 :
                                1;
                        }
                    }
                }
            }
        };

        this.animate = function() {
            // main animation loop
            // move, check status, die etc.
            s.move();
        };

        this.setVelocities = function() {
            s.vX = vRndX + rnd(storm.vMaxX * 0.12, 0.1);
            s.vY = vRndY + rnd(storm.vMaxY * 0.12, 0.1);
        };

        this.setOpacity = function(o, opacity) {
            if (!opacitySupported) {
                return false;
            }
            o.style.opacity = opacity;
        };

        this.melt = function() {
            if (!storm.useMeltEffect || !s.melting) {
                s.recycle();
            } else {
                if (s.meltFrame < s.meltFrameCount) {
                    s.setOpacity(s.o, s.meltFrames[s.meltFrame]);
                    s.o.style.fontSize =
                        s.fontSize -
                        s.fontSize * (s.meltFrame / s.meltFrameCount) +
                        "px";
                    s.o.style.lineHeight =
                        storm.flakeHeight +
                        2 +
                        storm.flakeHeight *
                        0.75 *
                        (s.meltFrame / s.meltFrameCount) +
                        "px";
                    s.meltFrame++;
                } else {
                    s.recycle();
                }
            }
        };

        this.recycle = function() {
            s.o.style.display = "none";
            s.o.style.position = fixedForEverything ? "fixed" : "absolute";
            s.o.style.bottom = "auto";
            s.setVelocities();
            s.vCheck();
            s.meltFrame = 0;
            s.melting = false;
            s.setOpacity(s.o, 1);
            s.o.style.padding = "0px";
            s.o.style.margin = "0px";
            s.o.style.fontSize = s.fontSize + "px";
            s.o.style.lineHeight = storm.flakeHeight + 2 + "px";
            s.o.style.textAlign = "center";
            s.o.style.verticalAlign = "baseline";
            s.x = parseInt(rnd(screenX - storm.flakeWidth - 20), 10);
            s.y = parseInt(rnd(screenY) * -1, 10) - storm.flakeHeight;
            s.refresh();
            s.o.style.display = "block";
            s.active = 1;
        };

        this.recycle(); // set up x/y coords etc.
        this.refresh();
    };

    this.snow = function() {
        var active = 0,
            flake = null,
            i,
            j;
        for (i = 0, j = storm.flakes.length; i < j; i++) {
            if (storm.flakes[i].active === 1) {
                storm.flakes[i].move();
                active++;
            }
            if (storm.flakes[i].melting) {
                storm.flakes[i].melt();
            }
        }
        if (active < storm.flakesMaxActive) {
            flake = storm.flakes[parseInt(rnd(storm.flakes.length), 10)];
            if (flake.active === 0) {
                flake.melting = true;
            }
        }
        if (storm.timer) {
            features.getAnimationFrame(storm.snow);
        }
    };

    this.mouseMove = function(e) {
        if (!storm.followMouse) {
            return true;
        }
        var x = parseInt(e.clientX, 10);
        if (x < screenX2) {
            windOffset = -windMultiplier + (x / screenX2) * windMultiplier;
        } else {
            x -= screenX2;
            windOffset = (x / screenX2) * windMultiplier;
        }
    };

    this.createSnow = function(limit, allowInactive) {
        var i;
        for (i = 0; i < limit; i++) {
            storm.flakes[storm.flakes.length] = new storm.SnowFlake(
                parseInt(rnd(flakeTypes), 10)
            );
            if (allowInactive || i > storm.flakesMaxActive) {
                storm.flakes[storm.flakes.length - 1].active = -1;
            }
        }
        storm.targetElement.appendChild(docFrag);
    };

    this.timerInit = function() {
        storm.timer = true;
        storm.snow();
    };

    this.init = function() {
        var i;
        for (i = 0; i < storm.meltFrameCount; i++) {
            storm.meltFrames.push(1 - i / storm.meltFrameCount);
        }
        storm.randomizeWind();
        storm.createSnow(storm.flakesMax); // create initial batch
        storm.events.add(window, "resize", storm.resizeHandler);
        storm.events.add(window, "scroll", storm.scrollHandler);
        if (storm.freezeOnBlur) {
            if (isIE) {
                storm.events.add(document, "focusout", storm.freeze);
                storm.events.add(document, "focusin", storm.resume);
            } else {
                storm.events.add(window, "blur", storm.freeze);
                storm.events.add(window, "focus", storm.resume);
            }
        }
        storm.resizeHandler();
        storm.scrollHandler();
        if (storm.followMouse) {
            storm.events.add(
                isIE ? document : window,
                "mousemove",
                storm.mouseMove
            );
        }
        storm.animationInterval = Math.max(20, storm.animationInterval);
        storm.timerInit();
    };

    this.start = function(bFromOnLoad) {
        if (!didInit) {
            didInit = true;
        } else if (bFromOnLoad) {
            // already loaded and running
            return true;
        }
        if (typeof storm.targetElement === "string") {
            var targetID = storm.targetElement;
            storm.targetElement = document.getElementById(targetID);
            if (!storm.targetElement) {
                throw new Error(
                    'Snowstorm: Unable to get targetElement "' + targetID + '"'
                );
            }
        }
        if (!storm.targetElement) {
            storm.targetElement = document.body || document.documentElement;
        }
        if (
            storm.targetElement !== document.documentElement &&
            storm.targetElement !== document.body
        ) {
            // re-map handler to get element instead of screen dimensions
            storm.resizeHandler = storm.resizeHandlerAlt;
            //and force-enable pixel positioning
            storm.usePixelPosition = true;
        }
        storm.resizeHandler(); // get bounding box elements
        storm.usePositionFixed =
            storm.usePositionFixed && !noFixed && !storm.flakeBottom; // whether or not position:fixed is to be used
        if (window.getComputedStyle) {
            // attempt to determine if body or user-specified snow parent element is relatlively-positioned.
            try {
                targetElementIsRelative =
                    window
                    .getComputedStyle(storm.targetElement, null)
                    .getPropertyValue("position") === "relative";
            } catch (e) {
                // oh well
                targetElementIsRelative = false;
            }
        }
        fixedForEverything = storm.usePositionFixed;
        if (screenX && screenY && !storm.disabled) {
            storm.init();
            storm.active = true;
        }
    };

    function doDelayedStart() {
        window.setTimeout(function() {
            storm.start(true);
        }, 20);
        // event cleanup
        storm.events.remove(
            isIE ? document : window,
            "mousemove",
            doDelayedStart
        );
    }

    function doStart() {
        if (!storm.excludeMobile || !isMobile) {
            doDelayedStart();
        }
        // event cleanup
        storm.events.remove(window, "load", doStart);
    }

    // hooks for starting the snow
    if (storm.autoStart) {
        storm.events.add(window, "load", doStart, false);
    }

    return this;
})(window, document);
