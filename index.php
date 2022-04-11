<?php
/*
 *  This file is part of P5.js playground <https://p5.javascript.org.pl>
 *
 *  Copyright (C) Jakub T. Jankiewicz <https://jcubic.pl/me>
 */

if (!is_valid_room() && !is_facebook()) {
    $url = self_url();
    $room = generate_name();
    if (preg_match("/index.php/", $url)) {
        $url .= "?room=$room";
    } else {
        if (!preg_match("|[/:]$|", $url)) {
            $url .= "/";
        }
        $url .= $room;
    }
    if (!empty($_SERVER['QUERY_STRING'])) {
        $url .= "?" . $_SERVER['QUERY_STRING'];
    }
    header('Location: ' . $url, true, 302);
    die();
}

function is_facebook() {
    return strpos($_SERVER["HTTP_USER_AGENT"], "facebookexternalhit/") !== false ||
           strpos($_SERVER["HTTP_USER_AGENT"], "Facebot") !== false;
}

function is_valid_room() {
    if (!isset($_GET['room']) || empty($_GET['room'])) {
        return false;
    }
    return preg_match("/^\w+-\w+$/", $_GET['room']);
}

function generate_name() {
    $nouns = explode("\n", file_get_contents("dict/nouns.txt"));
    $adjectives = explode("\n", file_get_contents("dict/adjectives.txt"));
    $i = array_rand($nouns);
    $j = array_rand($adjectives);
    return strtolower($adjectives[$j] . '-' . $nouns[$i]);
}

function self_url() {
    return origin() . strtok($_SERVER[REQUEST_URI], '?');
}

function origin() {
    $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http");
    return "$protocol://$_SERVER[HTTP_HOST]";
}

$root = preg_replace("|/[^/]+$|", "/", $_SERVER['REQUEST_URI']);

$origin = origin();

?><!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>p5.js Playground</title>
    <!--[if IE]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link rel="apple-touch-icon" sizes="180x180" href="./favicon/apple-touch-icon.png"/>
    <link rel="icon" type="image/png" sizes="32x32" href="./favicon/favicon-32x32.png"/>
    <link rel="icon" type="image/png" sizes="16x16" href="./favicon/favicon-16x16.png"/>
    <link rel="manifest" href="./favicon/site.webmanifest"/>
    <link rel="mask-icon" href="./favicon/safari-pinned-tab.svg" color="#5bbad5"/>
    <link rel="shortcut icon" href="./favicon/favicon.ico"/>
    <meta name="msapplication-TileColor" content="#000000"/>
    <meta name="msapplication-config" content="./favicon/browserconfig.xml"/>
    <meta name="theme-color" content="#ffffff"/>
    <link href="https://cdn.jsdelivr.net/npm/jquery.splitter/css/jquery.splitter.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/codemirror@5.x.x/lib/codemirror.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/combine/npm/codemirror@5.x.x/addon/search/matchesonscrollbar.css,npm/codemirror@5.x.x/addon/dialog/dialog.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/codemirror@5.x.x/theme/seti.css" rel="stylesheet"/>
    <script>var room = '<?= $_GET['room'] ?>'; var root = '<?= $root ?>';</script>
    <style>
     :root {
         --separator: gray;
         --tab-bg: #1d1e22;
         --tab-color: #aaaebc;
         --tab-border-color: #34363e;
         --tab-color-active: #1236f7;
     }
     body {
         margin: 0;
     }
     nav {
        background: black;
        position: relative;
        padding: 8px 10px;
        border-bottom: 2px solid var(--separator);
     }
     nav h1 {
        margin: 0;
        text-indent: -999999999px;
        width: 70px;
        height: 26px;
        background: black url(./brand-white.svg);
        background-size: contain;
     }
     main {
         height: calc(100vh - 27px - 44px);
     }
     iframe {
         border: none;
         display: block;
     }
     .CodeMirror {
         height: 100%;
     }
     .editor, .ast-explorer, .output, .user-input, iframe {
         width: 100%;
         height: 100%;
     }
     .hidden {
         display: none !important;
     }
     .errors ul, .tabs > ul, .config ul {
         list-style: none;
         margin: 0;
         padding: 0;
     }
     .tabs, .firepad {
         height: 100%;
     }
     .tabs > ul {
         padding: 5px 10px 0 5px;
         background: black;
         font-family: sans-serif;
         border-bottom: 2px solid var(--separator);
     }
     .tabs > ul li {
         background: var(--tab-bg);
         color: var(--tab-color);
         border-top: 3px solid var(--tab-border-color);
         border-radius: 3px 3px 0 0;
         display: inline-block;
     }
     .tabs > .content > :not(.active) {
         display: none;
     }
     .tabs > .content {
         height: calc(100% - 40px);
     }
     .tabs > ul li.active {
         border-top-color: var(--tab-color-active);
         margin-bottom: -2px;
         padding-bottom: 2px;
     }
     .tabs > ul li a, .tabs > ul li a:hover {
         color: inherit;
         display: block;
         padding: 6px 18px;
         text-decoration: none;
     }
     #term {
         height: 100%;
     }
     @media screen and (min-width: 900px) {
         iframe, aside {
             height: 100%;
         }
     }
     .CodeMirror-vscrollbar::-webkit-scrollbar {
         width: 6px;
         height: 6px;
         background: var(--background, #000);
     }
     .CodeMirror-vscrollbar::-webkit-scrollbar-thumb {
         background: var(--color, #aaa);
     }
     .CodeMirror-vscrollbar::-webkit-scrollbar-thumb:hover {
         background: var(--color, #aaa);
     }
     .CodeMirror-vscrollbar {
         scrollbar-color: #aaa #000;
         scrollbar-color: var(--color, #aaa) var(--background, #000);
         scrollbar-width: thin;
     }
     .firepad .CodeMirror-matchingbracket {
        text-decoration: none;
        color: white !important;
        background: #41535b;
     }
     .CodeMirror .syntax-error {
         background: red !important;
         color: black !important;
     }
     .config {
         position: absolute;
         right: 0;
         top: 0;
         padding: 2px;
         font-family: sans-serif;
     }
     .config ul {
         pointer-events: visible;
     }
     .config li {
         display: inline-block;
     }
     .config .console label {
         color: white;
     }
     .config li + li {
         margin-left: 6px;
     }
     .config button {
         width: 100%;
     }
     .errors {
         color: white;
         position: absolute;
         bottom: 0;
         right: 0;
         left: 0;
         z-index: 200;
     }
     .errors ul:not(:empty) {
         margin: 10px;
     }
     .errors li {
         position: relative;
         background: #CA1919;
         font-family: monospace;
         padding: 5px 20px 5px 10px;
         margin: 5px 0;
         box-shadow: 1px 2px 11px 2px rgba(0,0,0,0.42);
     }
     .errors .message {
         white-space: pre-wrap;
     }
     .errors .close {
         cursor: pointer;
         position: absolute;
         top: 5px;
         right: 5px;
         background: black;
         width: 16px;
         height: 16px;
         line-height: 14px;
         font-size: 16px;
         display: flex;
         justify-content: center;
         align-items: center;
         border-radius: 50%;
     }
     .btn {
         box-shadow:inset 0px -3px 7px 0px #29bbff;
         background:linear-gradient(to bottom, #2dabf9 5%, #0688fa 100%);
         background-color:#2dabf9;
         border-radius:3px;
         border:1px solid #0b0e07;
         display:inline-block;
         cursor:pointer;
         color:#ffffff;
         font-family:Arial;
         font-size:15px;
         padding:9px 23px;
         text-decoration:none;
         text-shadow:0px 1px 0px #263666;
     }
     .btn:hover {
         background:linear-gradient(to bottom, #0688fa 5%, #2dabf9 100%);
         background-color:#0688fa;
     }
     .btn:active {
         position:relative;
         top:1px;
     }
     footer {
         padding: 5px 0;
         font-family: monospace;
         text-align: center;
         background: var(--background, #000);
         color: var(--color, #ccc);
         border-top: 2px solid grey;
     }
     footer p {
         margin: 0;
     }
     footer a[href] {
         color: #3377FF;
         color: var(--link-color, #3377FF);
         cursor: pointer;
     }
     footer a[href]:hover {
         background-color: #3377FF;
         background-color: var(--link-color, #3377FF) !important;
         color: #000;
         color: var(--background, #000) !important;
         text-decoration: none;
     }
     #download {
        display: none; /* temporary */
     }
    </style>
    <link href="https://cdn.jsdelivr.net/combine/npm/prismjs/themes/prism-coy.css,npm/jquery.terminal/css/jquery.terminal.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery"></script>
    <script src="https://cdn.jsdelivr.net/npm/codemirror@5.x.x/lib/codemirror.js"></script>
    <script src="https://cdn.jsdelivr.net/combine/npm/codemirror@5.x.x/addon/search/search.js,npm/codemirror@5.x.x/addon/search/matchesonscrollbar.js,npm/codemirror@5.x.x/addon/search/searchcursor.js,npm/codemirror@5.x.x/addon/search/jump-to-line.js,npm/codemirror@5.x.x/addon/scroll/annotatescrollbar.js,npm/codemirror@5.x.x/addon/edit/matchbrackets.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/codemirror@5.x.x/addon/dialog/dialog.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/codemirror@5.x.x/mode/javascript/javascript.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/codemirror@5/addon/mode/simple.js"></script>
    <script src="https://cdn.jsdelivr.net/combine/npm/jquery.splitter,gh/jcubic/static/js/idb-keyval.js"></script>
    <script src="https://cdn.jsdelivr.net/combine/npm/jquery.terminal/js/jquery.terminal.min.js,npm/js-polyfills/keyboard.js,npm/prismjs/prism.min.js,npm/jquery.terminal/js/prism.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>

    <script src="https://cdn.firebase.com/libs/firepad/1.4.0/firepad.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
  <nav>
    <h1>P5.js</h1>
    <div class="config">
      <ul>
        <li class="console">
          <label for="console-mode">Console</label>
          <input type="checkbox" id="console-mode"/>
        </li>
        <li>
          <button id="reset" class="btn">Reset</button>
        </li>
        <li>
          <button id="download" class="btn">Download</button>
        </li>
      </ul>
    </div>
  </nav>
  <main>
  <aside class="editor-wrapper">
    <div class="tabs user-input">
      <ul>
        <li><a href="#">JavaScript</a></li>
      </ul>
      <div class="content">
        <div class="js-editor editor active"></div>
      </div>
    </div>
  </aside>
  <div class="output-wrapper">
    <div class="tabs">
      <ul>
        <li><a href="#">Preview</a></li>
      </ul>
      <div class="content">
        <div class="output active">
          <iframe id="frame"></iframe>
          <div class="errors">
            <ul></ul>
          </div>
        </div>
      </div>
    </div>
    <div class="dev hidden">
      <div id="term"></div>
    </div>
  </div>
</main>
<footer>
  <p>
    Copyright (C) 2022 <a href="https://jakub.jankiewicz.org">Jakub T. Jankiewicz</a>
    <a href="https://github.com/jcubic/p5">Source Code</a>
  </p>
</footer>
<script type="text/x-template" id="main_code">
function main() {
    {{CODE}}
}
</script>
<script type="text/x-template" id="template_code">
function setup() {
    createCanvas({{WIDTH}}, {{HEIGHT}});
}

{{MAIN}}

function draw() {
    try {
        main();
    } catch (e) {
        window.parent.postMessage({
            type: 'error',
            message: e.message,
            colno: null,
            lineno: null
        });
    }
    noLoop();
}
</script>
<script>

 $.fn.cm_refresh = function() {
     return this.filter('.CodeMirror').each(function() {
         this.CodeMirror.refresh();
     });
 };

 const urlSearchParams = new URLSearchParams(window.location.search);
 const query = Object.fromEntries(urlSearchParams.entries());

 // ref: https://www.freecodecamp.org/news/javascript-debounce-example/
 function debounce(func, timeout = 300) {
     let timer;
     return (...args) => {
         clearTimeout(timer);
         timer = setTimeout(() => { func.apply(this, args); }, timeout);
     };
 }

 function tab_activate(li) {
     var $li = $(li);
     var index = $li.index();
     $li.addClass('active').siblings().removeClass('active');
     var $content = $li.closest('ul').next();
     var $tab = $content.children()
                        .eq(index)
                        .addClass('active');
     $tab.siblings()
         .removeClass('active');
     if ($tab.is('.ast-explorer') && !$tab.is('.splitter_panel')) {
         $('.ast-explorer').split({
             orientation: 'horizontal'
         });
     }
     $tab.find('.CodeMirror').cm_refresh();
 }

 var worker;
 if ('serviceWorker' in navigator) {
     var scope = location.pathname.replace(/\/[^\/]+$/, '/');
     worker = navigator.serviceWorker.register('sw.js', {scope})
              .then(function(reg) {
                  reg.addEventListener('updatefound', function() {
                      var installing_worker = reg.installing;
                      console.log('A new service worker is being installed:',
                                  installing_worker);
                  });
                  // registration worked
                  console.log('Registration succeeded. Scope is ' + reg.scope);
              }).catch(function(error) {
                  // registration failed
                  console.log('Registration failed with ' + error);
              });
 }

 var ls = {
     get: function(name) {
         var value = localStorage.getItem(name);
         if (value) {
             return JSON.parse(value);
         }
         return value;
     },
     set: function(name, value) {
         localStorage.setItem(name, value);
     }
 };

 function get_from_query(variable, fallback = '') {
     if (variable) {
         return fetch_text(variable);
     }
     return fallback;
 }

 function get_main() {
     return get_from_query(query.main, main_code.innerHTML);
 }

 async function get_js_template() {
     if (query.template === 'none') {
         return '{{CODE}}';
     } else if (query.template) {
         return fetch_text(query.template);
     } else {
         return template(template_code.innerHTML, {
             MAIN: await get_main()
         });
     }
 }

 function load_base() {
     return get_from_query(query.base);
 }

 function get_includes() {
     if (query.include) {
         return query.include.split(',').map(file => {
             return `<script src="${root}${file}"></` + 'script>';
         }).join('');
     }
     return '';
 }

 function fetch_text(url) {
     return fetch(url).then(res => res.text());
 }

 async function get_file(token, url, file = '', fallback = () => fetch_text(url + file)) {
     async function next() {
         let result = await idbKeyval.get(token);
         if (!result) {
             result = await fallback();
         }
         return result;
     }
     try {
         if (query_mapping[token]) {
             const prop = query_mapping[token];
             const file = query[prop];
             if (file) {
                 return await fetch_text(url + file);
             }
         }
         return next();
     } catch(e) {
         return next();
     }
 }

 function get_template(token, url, node) {
     return get_file(token, url, null, () => node.innerHTML);
 }

 function to_json(object) {
     return JSON.stringify(object, true, 2);
 }

 function clear_marks(editor) {
     const loc = { line: 0, ch: 0 };
     const doc = editor.getDoc();
     doc.markText(loc, loc);
 }

 function mark_error(editor, message, start, end) {
     const doc = editor.getDoc();
     doc.markText(start, end, {
         className: 'syntax-error'
     })
 }

 function error(editor, message) {
     var $editor = $(editor);
     var ul = $editor.find('.errors ul');
     var found = ul.find('> li').filter(function() {
         return $(this).data('message') === message;
     });
     if (!found.length) {
         var $li = $(`<li>
              <span class="message">${message}</span>
              <span class="close">&times;</span>
           </li>`).data('message', message);
         $li.appendTo(ul)
     }
 }

 function clear_error(editor) {
     var $editor = $(editor);
     $editor.find('.errors ul').empty();
 }

 function better_tab(cm) {
     if (cm.somethingSelected()) {
         cm.indentSelection("add");
     } else {
         var spaces = Array(cm.getOption("indentUnit") + 1).join(" ");
         cm.replaceSelection(
             cm.getOption("indentWithTabs")? "\t" : spaces,
             "end",
             "+input"
         );
     }
 }

 function update_editor(cm, code) {
     const { left, top } = cm.getScrollInfo();
     cm.setValue(code);
     cm.scrollTo(left, top);
 }

 function download(blob, filename) {
     const url = URL.createObjectURL(blob);
     const $a = $(`<a href="${url}" download="${filename}">download</a>`).hide();
     $a.appendTo('body');
     $a[0].click();
     $a.remove();
     URL.revokeObjectURL(url);
 }

 // ref: https://stackoverflow.com/q/67322922/387194
 var __EVAL = (s) => {
     const window = frame.contentWindow;
     return window.eval(`void (__EVAL = ${__EVAL.toString()}); ${s}`);
 };

  function repr(object) {
     if (object) {
         if (typeof object === 'object') {
             var name = object.constructor.name;
             if (name) {
                 return `#&lt;${name}&gt;`;
             } else {
                 return object.toString();
             }
         } else {
             return new String(object);
         }
     }
 }

 function repl(fn) {
     return function(code) {
         if (code !== '') {
             try {
                 var result = fn(code);
                 if (result) {
                     this.echo(repr(result));
                 }
             } catch(e) {
                 this.error(new String(e));
             }
         }
     };
 }

 function template(string, mapping) {
     Object.entries(mapping).forEach(([key, value]) => {
         string = string.replace(new RegExp(`\\{\\{${key}\\}\\}`, 'g'), value);
     });
     return string;
 }

 (async function($) {
     const firebase_config = {
         apiKey: "AIzaSyD6lTSZ09MvFeDXL7vAXf7v3u7s32e9jG0",
         authDomain: "jcubic-p5.firebaseapp.com",
         databaseURL: "https://jcubic-p5-default-rtdb.europe-west1.firebasedatabase.app",
         projectId: "jcubic-p5",
         storageBucket: "jcubic-p5.appspot.com",
         messagingSenderId: "158560894455",
         appId: "1:158560894455:web:d95ad6e0a417348e6702f2"
     };
     const app = firebase.initializeApp(firebase_config);
     const database = app.database();
     const roomRef = database.ref().child(room);

     const has_worker = !!worker;
     await worker;
     const media_query = 'screen and (max-width: 900px)';
     const small = matchMedia(media_query).matches
     const orientation = small ? 'horizontal' : 'vertical';
     $('main').split({
         orientation
     });
     $('.errors').on('click', '.close', function() {
         $(this).closest('li').remove();
     });
     $('#reset').on('click', async function() {
         state.innput = await load_base();
         state.firepad.setText(state.innput);
         return false;
     });
     $('#download').on('click', function() {
         const zip = new JSZip();
         const game = zip.folder("p5");
         game.file("index.js", state.javascript);
         game.file("index.html", state.html);
         zip.generateAsync({ type:"blob" }).then(function(content) {
             download(content, "p5.zip");
         });
     });
     let console_splitter;

     var $dev_toggle = $('#console-mode').on('change', function() {
         toggle_dev_mode(this.checked);
     });

     $.terminal.syntax('javascript');
     $.terminal.prism_formatters = {
        animation: true,
        command: true
     };
     let term;
     $('iframe').on('load', () => {
         term = $('#term').terminal([{
             reset: function() {
                 this.clear();
                 this.echo('JavaScript Console');
             }
         }, repl((code) => frame.contentWindow.__EVAL(code))], {
             greetings: 'JavaScript Console',
             outputLimit: 200,
             completion: Object.keys(frame.contentWindow)
         });
     });

     const { get, set } = idbKeyval;

     const INPUT_CODE = '__code__';
     const DEV_MODE = 'p5__dev__mode';
     const SCRIPT_FILE = '_p5.js';
     const HTML_FILE = '_p5.html'

     const html = template(await fetch_text('./base.html'), {
         HTML: get_includes()
     });

     const state = {
         dev_mode: ls.get(DEV_MODE),
         input: await load_base(),
         html,
         editors: {}
     };
     window.state = state;

     const js_template = await get_js_template();

     state.editors.input = CodeMirror($('.js-editor').get(0), {
         theme: 'seti',
         lineWrapping: true,
         lineNumbers: true,
         matchBrackets: true,
         extraKeys: { Tab: better_tab, "Alt-F": "findPersistent" },
         indentUnit: 4,
         mode: 'javascript'
     });

     state.firepad = Firepad.fromCodeMirror(roomRef, state.editors.input, {
         defaultText: state.input
     });
     window.addEventListener('message', async function(event) {
         const { data } = event;
         if (data) {
             if (data.type === 'error') {
                 show_error(data);
             } else if (data.type === 'echo') {
                 data.args.forEach(arg => {
                     term.echo(arg);
                 });
             }
         }
     });
     
     state.editors.input.on('change', debounce(update, 800));
     
     if (state.dev_mode !== undefined) {
         toggle_dev_mode(state.dev_mode);
         $dev_toggle.prop('checked', state.dev_mode);
     }

     state.firepad.on('ready', update);

     async function show_error(data) {
         const { lineno, colno, message, source } = data;
         if (lineno === null || colno === null) {
             return error('.output', `Error: ${message}`);
         }
         const msg = `Error: ${message}\nline: ${lineno} col: ${colno}`;
         error('.output', msg);
         if (state.full_output_mode) {
             const code = await fetch(source).then(res => res.text());
             const lines = code.split('\n');
             const line = lines[lineno - 1];
             var start = {
                 line: lineno -1,
                 ch: line.match(/^(\s*)/)[0].length
             }
             const end = { line: lineno - 1, ch: line.length };
             state.editors.output.scrollIntoView(start);
             mark_error(
                 state.editors.output,
                 message,
                 start,
                 end
             );
         }
     }

     function toggle_dev_mode(enable) {
         state.dev_mode = enable;
         ls.set(DEV_MODE, state.dev_mode);
         $('.dev').toggleClass('hidden', !state.dev_mode);
         if (enable) {
             if (!console_splitter) {
                 console_splitter = $('.output-wrapper').split({
                     orientation: 'horizontal'
                 });
             }
         } else if (console_splitter) {
             console_splitter.destroy();
             console_splitter = null;
         }
     }

     async function set_idb() {
         await set(INPUT_CODE, state.input);
         await set(HTML_FILE, state.html);
         await set(SCRIPT_FILE, state.javascript);
     }

     async function update() {
         clear_error('.output');
         state.input = state.editors.input.getValue();
         state.javascript = get_javascript(state.input);
         await set_idb();
         term && term.exec('reset', true);
         frame.src = `./__idb__/${HTML_FILE}`;
     }

     function get_javascript(input) {
         return template(js_template, {
             WIDTH: 400,
             HEIGHT: 400,
             CODE: input
         });
     }
 })(jQuery);
</script>
<!-- Start Open Web Analytics Tracker -->
<script type="text/javascript">
//<![CDATA[
var owa_baseUrl = 'https://stats.jcubic.pl/';
var owa_cmds = owa_cmds || [];
owa_cmds.push(['setSiteId', 'af9e9c0a33f55cbef865e63d269f4be7']);
owa_cmds.push(['trackPageView']);
owa_cmds.push(['trackClicks']);

(function() {
    var _owa = document.createElement('script'); _owa.type = 'text/javascript'; _owa.async = true;
    owa_baseUrl = ('https:' == document.location.protocol ? window.owa_baseSecUrl || owa_baseUrl.replace(/http:/, 'https:') : owa_baseUrl );
    _owa.src = owa_baseUrl + 'modules/base/js/owa.tracker-combined-min.js';
    var _owa_s = document.getElementsByTagName('script')[0]; _owa_s.parentNode.insertBefore(_owa, _owa_s);
}());
//]]>
</script>
<!-- End Open Web Analytics Code -->
<script defer async src="https://cdn.jsdelivr.net/npm/jszip"></script>
</body>
</html>
