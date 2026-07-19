/*!
 * Wazabiashara — Logo Intro Animation
 * Self-contained JS module. Include with a single <script> tag:
 *
 *   <script src="wazabiashara-intro.js"></script>
 *
 * By default it mounts full-screen into <body>. To mount inside your own
 * container instead, add a target element and pass its id:
 *
 *   <div id="wz-intro"></div>
 *   <script src="wazabiashara-intro.js"></script>
 *   <script>WazabiasharaIntro.mount('wz-intro');</script>
 *
 * Auto-mounts to <body> on load unless data-auto="false" is set on the
 * <script> tag, e.g. <script src="wazabiashara-intro.js" data-auto="false"></script>
 */
(function (global) {
  "use strict";

  var LOGO_SVG =
    '<svg viewBox="0 0 512 529" xmlns="http://www.w3.org/2000/svg">' +
    '<g transform="translate(0,529) scale(0.1,-0.1)">' +
    '<path d="M397 4035 c-186 -51 -338 -210 -383 -401 -40 -172 19 -369 148 -493 23 -24 83 -67 133 -98 468 -289 828 -661 1030 -1064 126 -250 195 -524 195 -777 0 -84 19 -195 35 -210 5 -4 5 2 1 13 -14 37 -18 170 -7 229 13 72 47 147 110 246 240 374 327 845 256 1385 -29 215 -26 208 -153 349 -199 219 -477 457 -737 631 -174 116 -272 169 -351 189 -74 19 -210 19 -277 1z" fill="#51AED9"/>' +
    '<path d="M1560 965 c0 -3 2 -5 5 -5 3 0 5 2 5 5 0 3 -2 5 -5 5 -3 0 -5 -2 -5 -5z" fill="#51AED9"/>' +
    '<path d="M1983 4605 c-108 -29 -213 -102 -286 -197 -79 -105 -113 -219 -105 -358 5 -77 13 -103 81 -275 270 -678 340 -1293 207 -1809 -47 -184 -106 -317 -213 -486 -88 -137 -109 -195 -115 -311 -5 -114 7 -175 55 -278 148 -311 575 -389 836 -151 149 136 365 554 461 890 86 302 111 497 110 855 0 300 -13 450 -60 717 -63 361 -205 825 -338 1098 -74 154 -205 264 -362 305 -72 19 -201 18 -271 0z" fill="#DB3A4D"/>' +
    '<path d="M2285 4603 c101 -35 170 -79 237 -152 66 -71 99 -133 181 -341 182 -461 295 -966 314 -1398 l6 -142 64 -108 c284 -479 431 -991 450 -1567 4 -104 10 -179 13 -165 4 14 34 88 68 165 230 537 343 1041 368 1650 6 155 -9 512 -25 602 -19 99 -316 478 -591 754 -245 246 -505 456 -752 610 -131 81 -210 109 -328 115 l-85 5 80 -28z" fill="#FFE167"/>' +
    '<path d="M3972 5270 c-105 -28 -167 -64 -253 -150 -87 -86 -129 -162 -155 -277 -31 -142 -12 -246 85 -461 97 -213 207 -577 261 -863 56 -298 71 -444 77 -751 6 -316 -3 -485 -38 -733 -53 -373 -163 -766 -312 -1110 -83 -193 -107 -275 -107 -360 0 -135 60 -290 147 -379 227 -234 585 -242 813 -18 57 56 76 85 123 182 160 334 315 809 396 1215 185 918 134 1881 -145 2781 -83 269 -213 589 -280 689 -133 201 -380 296 -612 235z" fill="#31D382"/>' +
    "</g></svg>";

  var WORD = "wazabiashara";
  var TAGLINE = "Biashara Yako, Mkononi Mwako";
  var CSS_ID = "wz-intro-styles";
  var FONT_ID = "wz-intro-font";

  var CSS = "" +
    ".wz-stage{" +
      "position:fixed;inset:0;z-index:99999;width:100%;height:100%;min-height:100vh;" +
      "display:flex;align-items:center;justify-content:center;background:#FFFFFF;overflow:hidden;" +
      "font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;" +
      "transition:opacity .6s ease-out;" +
      "--wz-logo:clamp(70px,16vmin,180px);" +
      "--wz-logo-h:calc(var(--wz-logo) * 1.027);" +
      "--wz-font:clamp(22px,5.2vmin,60px);" +
      "--wz-slide:calc(var(--wz-logo) * 1.27);" +
      "--wz-drop:calc(var(--wz-logo) * -2.8);" +
      "--wz-gap:calc(var(--wz-logo) * 0.12);" +
      "--wz-offset:calc(var(--wz-logo) * -0.27);" +
      "--wz-shadow-y:calc(var(--wz-logo) * 0.79);" +
      "--wz-shadow-w:var(--wz-logo);" +
      "--wz-shadow-h:calc(var(--wz-logo) * 0.147);" +
      "--wz-tagline-y:calc(var(--wz-logo) * 0.213);" +
      "--wz-tagline-fs:clamp(9px,1.5vmin,14px);" +
      "--wz-letter-y:calc(var(--wz-font) * -0.41);" +
      "--wz-blur:calc(var(--wz-logo) * 0.013);" +
    "}" +
    ".wz-stage.wz-hide{opacity:0;pointer-events:none;}" +
    ".wz-ground-shadow{position:absolute;width:var(--wz-shadow-w);height:var(--wz-shadow-h);left:50%;top:calc(50% + var(--wz-shadow-y));transform:translate(-50%,0) scaleX(0.3);background:radial-gradient(ellipse at center, rgba(20,25,35,0.16) 0%, rgba(20,25,35,0) 72%);border-radius:50%;opacity:0;animation:wzShadowGrow .5s ease-out 1.05s forwards, wzShadowSlide .9s cubic-bezier(.65,0,.35,1) 1.75s forwards;filter:blur(calc(var(--wz-blur) * 150px));}" +
    ".wz-logo-wrap{position:absolute;display:flex;align-items:center;justify-content:center;width:var(--wz-logo);height:var(--wz-logo-h);transform:translate(0,var(--wz-drop)) scale(0.82);opacity:0;animation:wzDropIn 1.05s cubic-bezier(.31,1.6,.55,1) .15s forwards, wzSlideLeft .9s cubic-bezier(.65,0,.35,1) 1.75s forwards;}" +
    ".wz-logo-wrap svg{width:100%;height:100%;display:block;filter:drop-shadow(0 calc(var(--wz-logo) * 0.093) calc(var(--wz-logo) * 0.147) rgba(20,25,35,0.18));animation:wzSquash .42s ease-out 1.05s;transform-origin:50% 88%;}" +
    ".wz-wordmark{position:absolute;left:50%;top:50%;display:flex;align-items:baseline;transform:translate(var(--wz-offset),-50%);white-space:nowrap;padding-left:var(--wz-gap);}" +
    ".wz-wordmark span{display:inline-block;font-weight:900;font-size:var(--wz-font);letter-spacing:-0.02em;opacity:0;transform:translateY(var(--wz-letter-y)) scale(0.4) rotate(-14deg);animation:wzLetterBuild .5s cubic-bezier(.34,1.56,.64,1) forwards;font-family:'Nunito',ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#024938;}" +
    ".wz-tagline{position:absolute;left:50%;top:50%;transform:translate(var(--wz-offset),var(--wz-tagline-y));padding-left:var(--wz-gap);font-size:var(--wz-tagline-fs);font-weight:700;letter-spacing:0.22em;text-transform:uppercase;color:#f9ac00;font-family:'Nunito',ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;opacity:0;animation:wzFadeUp .7s ease-out 3.05s forwards;}" +
    "@keyframes wzDropIn{0%{transform:translate(0,var(--wz-drop)) scale(0.82);opacity:0;}8%{opacity:1;}58%{transform:translate(0,calc(var(--wz-logo) * 0.093)) scale(1.04);}72%{transform:translate(0,calc(var(--wz-logo) * -0.067)) scale(0.98);}86%{transform:translate(0,calc(var(--wz-logo) * 0.027)) scale(1.01);}100%{transform:translate(0,0) scale(1);opacity:1;}}" +
    "@keyframes wzSquash{0%{transform:scale(1,1);}30%{transform:scale(1.18,0.8);}55%{transform:scale(0.94,1.08);}75%{transform:scale(1.04,0.97);}100%{transform:scale(1,1);}}" +
    "@keyframes wzSlideLeft{0%{transform:translate(0,0) scale(1);}100%{transform:translate(calc(var(--wz-slide) * -1),0) scale(1);}}" +
    "@keyframes wzShadowGrow{0%{opacity:0;transform:translate(-50%,0) scaleX(0.15);}100%{opacity:1;transform:translate(-50%,0) scaleX(1);}}" +
    "@keyframes wzShadowSlide{0%{transform:translate(-50%,0) scaleX(1) translateX(0);}100%{transform:translate(-50%,0) scaleX(0.75) translateX(calc(var(--wz-slide) * -1));}}" +
    "@keyframes wzLetterBuild{0%{opacity:0;transform:translateY(var(--wz-letter-y)) scale(0.4) rotate(-14deg);}55%{opacity:1;transform:translateY(calc(var(--wz-font) * 0.063)) scale(1.08) rotate(3deg);}78%{transform:translateY(calc(var(--wz-font) * -0.031)) scale(0.97) rotate(-1deg);}100%{opacity:1;transform:translateY(0) scale(1) rotate(0deg);}}" +
    "@keyframes wzFadeUp{0%{opacity:0;transform:translate(var(--wz-offset),calc(var(--wz-tagline-y) + 12px));}100%{opacity:1;transform:translate(var(--wz-offset),var(--wz-tagline-y));}}" +
    ".wz-replay{position:absolute;bottom:calc(var(--wz-logo) * 0.227);left:50%;transform:translateX(-50%);background:#F4F6F9;border:1px solid #E3E7EE;color:#5B6472;font-size:var(--wz-tagline-fs);font-weight:700;font-family:'Nunito',ui-sans-serif,system-ui,sans-serif;letter-spacing:0.05em;padding:calc(var(--wz-logo) * 0.067) calc(var(--wz-logo) * 0.133);border-radius:999px;cursor:pointer;transition:background .2s ease,border-color .2s ease,color .2s ease,transform .15s ease;}" +
    ".wz-replay:hover{background:#FFF8E8;border-color:#f9ac00;color:#8A6A00;transform:translateX(-50%) translateY(-1px);}" +
    "@media (max-width:480px){.wz-stage{--wz-logo:clamp(60px,22vmin,120px);--wz-font:clamp(18px,6.5vmin,36px);--wz-tagline-fs:clamp(8px,2.2vmin,11px);}}" +
    "@media (min-width:1200px){.wz-stage{--wz-logo:clamp(120px,14vmin,200px);--wz-font:clamp(40px,4.8vmin,72px);}}";

  function injectFont() {
    if (document.getElementById(FONT_ID)) return;
    var link = document.createElement("link");
    link.id = FONT_ID;
    link.rel = "stylesheet";
    link.href = "https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap";
    document.head.appendChild(link);
  }

  function injectCSS() {
    if (document.getElementById(CSS_ID)) return;
    var style = document.createElement("style");
    style.id = CSS_ID;
    style.textContent = CSS;
    document.head.appendChild(style);
  }

  function buildWordSpans() {
    var frag = document.createDocumentFragment();
    var baseDelay = 1.85;
    var step = 0.08;
    for (var i = 0; i < WORD.length; i++) {
      var span = document.createElement("span");
      span.textContent = WORD[i];
      span.style.animationDelay = (baseDelay + i * step).toFixed(2) + "s";
      frag.appendChild(span);
    }
    return frag;
  }

  function buildStage() {
    var stage = document.createElement("div");
    stage.className = "wz-stage";

    var shadow = document.createElement("div");
    shadow.className = "wz-ground-shadow";
    stage.appendChild(shadow);

    var logoWrap = document.createElement("div");
    logoWrap.className = "wz-logo-wrap";
    logoWrap.innerHTML = LOGO_SVG;
    stage.appendChild(logoWrap);

    var wordmark = document.createElement("div");
    wordmark.className = "wz-wordmark";
    wordmark.appendChild(buildWordSpans());
    stage.appendChild(wordmark);

    var tagline = document.createElement("div");
    tagline.className = "wz-tagline";
    tagline.textContent = TAGLINE;
    stage.appendChild(tagline);

    return stage;
  }

  var SESSION_KEY = "wz-intro-played";
  var ANIM_DURATION = 4200;

  function shouldPlay() {
    try {
      return sessionStorage.getItem(SESSION_KEY) !== "1";
    } catch (e) {
      return true;
    }
  }

  function markPlayed() {
    try {
      sessionStorage.setItem(SESSION_KEY, "1");
    } catch (e) {}
  }

  var WazabiasharaIntro = {
    /**
     * Mount and play the intro animation.
     * @param {string|HTMLElement} [target] - element or id to mount into. Defaults to document.body (full screen).
     * @param {Object} [opts]
     * @param {boolean} [opts.showReplay=true] - show a "Cheza Tena" replay button.
     * @param {boolean} [opts.autoDismiss=true] - auto fade out after animation completes.
     * @param {number} [opts.duration=4200] - ms before auto dismiss.
     * @param {boolean} [opts.oncePerSession=true] - only play once per browser session.
     * @returns {{replay: Function, el: HTMLElement, dismiss: Function}}
     */
    mount: function (target, opts) {
      opts = opts || {};
      var showReplay = opts.showReplay !== false;
      var autoDismiss = opts.autoDismiss !== false;
      var duration = opts.duration || ANIM_DURATION;
      var oncePerSession = opts.oncePerSession !== false;

      if (oncePerSession && !shouldPlay()) {
        return { replay: function(){}, el: null, dismiss: function(){} };
      }

      injectFont();
      injectCSS();

      var container =
        typeof target === "string"
          ? document.getElementById(target)
          : target instanceof HTMLElement
          ? target
          : document.body;

      if (!container) {
        console.error("WazabiasharaIntro: target not found, mounting to body.");
        container = document.body;
      }

      var stage = buildStage();
      container.appendChild(stage);

      markPlayed();

      function dismiss() {
        stage.classList.add("wz-hide");
        setTimeout(function () {
          if (stage.parentNode) stage.parentNode.removeChild(stage);
          var btn = container.querySelector(".wz-replay");
          if (btn) btn.remove();
        }, 650);
      }

      function addReplayBtn() {
        var btn = document.createElement("button");
        btn.className = "wz-replay";
        btn.type = "button";
        btn.textContent = "Cheza Tena \u21bb";
        btn.addEventListener("click", replay);
        container.appendChild(btn);
      }

      function replay() {
        var existing = container.querySelector(".wz-replay");
        if (existing) existing.remove();
        if (stage.parentNode) stage.parentNode.removeChild(stage);
        stage = buildStage();
        container.appendChild(stage);
        if (autoDismiss) {
          setTimeout(dismiss, duration);
        }
        if (showReplay) {
          addReplayBtn();
        }
      }

      if (autoDismiss) {
        setTimeout(dismiss, duration);
      }

      if (showReplay) {
        addReplayBtn();
      }

      return { replay: replay, el: stage, dismiss: dismiss };
    }
  };

  global.WazabiasharaIntro = WazabiasharaIntro;

  // Auto-mount to <body> unless explicitly disabled via data-auto="false"
  var currentScript = document.currentScript;
  var auto = !currentScript || currentScript.getAttribute("data-auto") !== "false";
  if (auto) {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", function () {
        WazabiasharaIntro.mount(document.body, { showReplay: false, autoDismiss: true, oncePerSession: true });
      });
    } else {
      WazabiasharaIntro.mount(document.body, { showReplay: false, autoDismiss: true, oncePerSession: true });
    }
  }
})(window);
