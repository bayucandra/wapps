<?php
	session_start();
	define("IS_ROOT", true);//FOR CHECKING IF IT MUST BE AN INCLUDE AND MAY NOT ACCESSED PARTIALLY
	require("res/php/functions/general.php");
	require("res/php/config.php");
	$_SESSION[SESSION_NM]["root_path"]=getcwd();
// 	require("res/php/connect/db.php");
// 	require("res/php/classes/badmin.php");
// 	$OBAdmin=new BAdmin($db);//purge files later
// 	$OBAdmin->logged_out_protect("login.php");//purge files later
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">

	<title>WApps</title>
<!-- 	BEGIN JS GLOBAL VARS -->
	<script type="text/javascript" src="res/js/bfunctions.js"></script>
	<script type="text/javascript">
		var app_detail=<?php echo json_encode(unserialize(APP_DETAIL));?>;
		var loading_html='<img alt="Loading..." src="res/images/loading.gif" /><h3 style="color:#999999" class="farial">Loading...</h3>';
		var compose_arr=[
			{"subject":"--NO SUBJECT--","id":0,"content":""},
			{"subject":"--NO SUBJECT--","id":15,"content":""}
		];
		var compose_idx=0;
	</script>
<!-- 	END JS GLOBAL VARS -->
	<link rel="stylesheet" type="text/css" href="res/css/main.css"/>
	<link rel="stylesheet" type="text/css" href="res/css/interface.css"/>
	<script type="text/javascript" src="res/ckeditor/ckeditor.js"></script>

	<!-- The line below must be kept intact for Sencha Cmd to build your application -->
	<script type="text/javascript">var Ext=Ext||{};Ext.manifest="app";Ext=Ext||window.Ext||{};
Ext.Boot=Ext.Boot||function(t){var l=document,p={disableCaching:/[?&](?:cache|disableCacheBuster)\b/i.test(location.search)||"file:"===location.href.substring(0,5)||/(^|[ ;])ext-cache=1/.test(l.cookie)?!1:!0,disableCachingParam:"_dc",loadDelay:!1,preserveScripts:!0,charset:void 0},u,q=[],n={},d=/\.css(?:\?|$)/i,m=/\/[^\/]*$/,e=l.createElement("a"),k="undefined"!==typeof window,v={browser:k,node:!k&&"function"===typeof require,phantom:"undefined"!==typeof phantom&&phantom.fs},w=[],g=0,s=0,h={loading:0,
loaded:0,env:v,config:p,scripts:n,currentFile:null,canonicalUrl:function(a){e.href=a;a=e.href;var b=p.disableCachingParam,b=b?a.indexOf(b+"\x3d"):-1,c,f;if(0<b&&("?"===(c=a.charAt(b-1))||"\x26"===c)){f=a.indexOf("\x26",b);if((f=0>f?"":a.substring(f))&&"?"===c)++b,f=f.substring(1);a=a.substring(0,b-1)+f}return a},init:function(){var a=l.getElementsByTagName("script"),b=a.length,c=/\/ext(\-[a-z\-]+)?\.js$/,f,r,d,j,g,e;for(e=0;e<b;e++)if(r=(f=a[e]).src)if(d=f.readyState||null,!j&&c.test(r)&&(h.hasAsync=
"async"in f||!("readyState"in f),j=r),!n[g=h.canonicalUrl(r)])n[g]=f={key:g,url:r,done:null===d||"loaded"===d||"complete"===d,el:f,prop:"src"},f.done||h.watch(f);j||(f=a[a.length-1],j=f.src,h.hasAsync="async"in f||!("readyState"in f));h.baseUrl=j.substring(0,j.lastIndexOf("/")+1)},create:function(a,b){var c=a&&d.test(a),f=l.createElement(c?"link":"script"),r;if(c)f.rel="stylesheet",r="href";else{f.type="text/javascript";if(!a)return f;r="src";h.hasAsync&&(f.async=!1)}b=b||a;return n[b]={key:b,url:a,
css:c,done:!1,el:f,prop:r,loaded:!1,evaluated:!1}},getConfig:function(a){return a?p[a]:p},setConfig:function(a,b){if("string"===typeof a)p[a]=b;else for(var c in a)h.setConfig(c,a[c]);return h},getHead:function(){return h.docHead||(h.docHead=l.head||l.getElementsByTagName("head")[0])},inject:function(a,b,c){var f=h.getHead(),r,g=!1,j=h.canonicalUrl(b);d.test(b)?(g=!0,r=l.createElement("style"),r.type="text/css",r.textContent=a,c&&("id"in c&&(r.id=c.id),"disabled"in c&&(r.disabled=c.disabled)),a=l.createElement("base"),
a.href=j.replace(m,"/"),f.appendChild(a),f.appendChild(r),f.removeChild(a)):(b&&(a+="\n//# sourceURL\x3d"+j),Ext.globalEval(a));b=n[j]||(n[j]={key:j,css:g,url:b,el:r});b.done=!0;return b},load:function(a){if(a.sync||s)return this.loadSync(a);a.url||(a={url:a});if(u)q.push(a);else{h.expandLoadOrder(a);var b=a.url,b=b.charAt?[b]:b,c=b.length,f;a.urls=b;a.loaded=0;a.loading=c;a.charset=a.charset||p.charset;a.buster=("cache"in a?!a.cache:p.disableCaching)&&p.disableCachingParam+"\x3d"+ +new Date;u=a;
a.sequential=!1;for(f=0;f<c;++f)h.loadUrl(b[f],a)}return this},loadUrl:function(a,b){var c,f=b.buster,d=b.charset,e=h.getHead(),j;b.prependBaseUrl&&(a=h.baseUrl+a);h.currentFile=b.sequential?a:null;j=h.canonicalUrl(a);if(c=n[j])c.done?h.notify(c,b):c.requests?c.requests.push(b):c.requests=[b];else if(g++,c=h.create(a,j),j=c.el,!c.css&&d&&(j.charset=d),c.requests=[b],h.watch(c),f&&(a+=(-1===a.indexOf("?")?"?":"\x26")+f),!h.hasAsync&&!c.css){c.loaded=!1;c.evaluated=!1;var k=function(){c.loaded=!0;var a=
b.urls,f=a.length,j,d;for(j=0;j<f;j++)if(d=h.canonicalUrl(a[j]),d=n[d])if(d.loaded)d.evaluated||(e.appendChild(d.el),d.evaluated=!0,d.onLoadWas.apply(d.el,arguments));else break};"readyState"in j?(f=j.onreadystatechange,j.onreadystatechange=function(){("loaded"===this.readyState||"complete"===this.readyState)&&k.apply(this,arguments)}):(f=j.onload,j.onload=k);c.onLoadWas=f;j[c.prop]=a}else j[c.prop]=a,e.appendChild(j)},loadSequential:function(a){a.url||(a={url:a});a.sequential=!0;h.load(a)},loadSequentialBasePrefix:function(a){a.url||
(a={url:a});a.prependBaseUrl=!0;h.loadSequential(a)},fetchSync:function(a){var b,c;b=!1;c=new XMLHttpRequest;try{c.open("GET",a,!1),c.send(null)}catch(f){b=!0}return{content:c.responseText,exception:b,status:1223===c.status?204:0===c.status&&("file:"===(self.location||{}).protocol||"ionp:"===(self.location||{}).protocol)?200:c.status}},loadSync:function(a){s++;a=h.expandLoadOrder(a.url?a:{url:a});var b=a.url,c=b.charAt?[b]:b,f=c.length,d=p.disableCaching&&"?"+p.disableCachingParam+"\x3d"+ +new Date,
e,j,k,m,l;a.loading=f;a.urls=c;a.loaded=0;g++;for(k=0;k<f;++k){b=c[k];a.prependBaseUrl&&(b=h.baseUrl+b);h.currentFile=b;e=h.canonicalUrl(b);if(j=n[e]){if(j.done){h.notify(j,a);continue}j.el&&(j.preserve=!1,h.cleanup(j));j.requests?j.requests.push(a):j.requests=[a]}else g++,n[e]=j={key:e,url:b,done:!1,requests:[a],el:null};j.sync=!0;d&&(b+=d);++h.loading;e=h.fetchSync(b);j.done=!0;l=e.exception;m=e.status;e=e.content||"";(l||0===m)&&!v.phantom?j.error=!0:200<=m&&300>m||304===m||v.phantom||0===m&&0<
e.length?h.inject(e,b):j.error=!0;h.notifyAll(j)}s--;g--;h.fireListeners();h.currentFile=null;return this},loadSyncBasePrefix:function(a){a.url||(a={url:a});a.prependBaseUrl=!0;h.loadSync(a)},notify:function(a,b){b.preserve&&(a.preserve=!0);++b.loaded;a.error&&(b.errors||(b.errors=[])).push(a);if(--b.loading)!s&&(b.sequential&&b.loaded<b.urls.length)&&h.loadUrl(b.urls[b.loaded],b);else{u=null;var c=b.errors,f=b[c?"failure":"success"],c="delay"in b?b.delay:c?1:p.chainDelay,d=b.scope||b;q.length&&h.load(q.shift());
f&&(0===c||0<c?setTimeout(function(){f.call(d,b)},c):f.call(d,b))}},notifyAll:function(a){var b=a.requests,c=b&&b.length,f;a.done=!0;a.requests=null;--h.loading;++h.loaded;for(f=0;f<c;++f)h.notify(a,b[f]);c||(a.preserve=!0);h.cleanup(a);g--;h.fireListeners()},watch:function(a){var b=a.el,c=a.requests,c=c&&c[0],f=function(){a.done||h.notifyAll(a)};b.onerror=function(){a.error=!0;h.notifyAll(a)};a.preserve=c&&"preserve"in c?c.preserve:p.preserveScripts;"readyState"in b?b.onreadystatechange=function(){("loaded"===
this.readyState||"complete"===this.readyState)&&f()}:b.onload=f;++h.loading},cleanup:function(a){var b=a.el,c;if(b){if(!a.preserve)for(c in a.el=null,b.parentNode.removeChild(b),b)try{c!==a.prop&&(b[c]=null),delete b[c]}catch(f){}b.onload=b.onerror=b.onreadystatechange=t}},fireListeners:function(){for(var a;!g&&(a=w.shift());)a()},onBootReady:function(a){g?w.push(a):a()},createLoadOrderMap:function(a){var b=a.length,c={},f,d;for(f=0;f<b;f++)d=a[f],c[d.path]=d;return c},getLoadIndexes:function(a,b,
c,f,d){var e=c[a],j,g,k,m,l;if(b[a])return b;b[a]=!0;for(a=!1;!a;){k=!1;for(m in b)if(b.hasOwnProperty(m)&&(e=c[m]))if(g=h.canonicalUrl(e.path),g=n[g],!d||!g||!g.done){g=e.requires;f&&e.uses&&(g=g.concat(e.uses));e=g.length;for(j=0;j<e;j++)l=g[j],b[l]||(k=b[l]=!0)}k||(a=!0)}return b},getPathsFromIndexes:function(a,b){var c=[],f=[],d,e;for(d in a)a.hasOwnProperty(d)&&a[d]&&c.push(d);c.sort(function(a,b){return a-b});d=c.length;for(e=0;e<d;e++)f.push(b[c[e]].path);return f},expandUrl:function(a,b,c,
d,e,g){"string"==typeof a&&(a=[a]);if(b){c=c||h.createLoadOrderMap(b);d=d||{};var j=a.length,k=[],m,n;for(m=0;m<j;m++)(n=c[a[m]])?h.getLoadIndexes(n.idx,d,b,e,g):k.push(a[m]);return h.getPathsFromIndexes(d,b).concat(k)}return a},expandUrls:function(a,b,c,d){"string"==typeof a&&(a=[a]);var e=[],g=a.length,j;for(j=0;j<g;j++)e=e.concat(h.expandUrl(a[j],b,c,{},d,!0));0==e.length&&(e=a);return e},expandLoadOrder:function(a){var b=a.url,c=a.loadOrder,d=a.loadOrderMap;a.expanded?c=b:(c=h.expandUrls(b,c,
d),a.expanded=!0);a.url=c;b.length!=c.length&&(a.sequential=!0);return a}};Ext.disableCacheBuster=function(a,b){var c=new Date;c.setTime(c.getTime()+864E5*(a?3650:-1));c=c.toGMTString();l.cookie="ext-cache\x3d1; expires\x3d"+c+"; path\x3d"+(b||"/")};h.init();return h}(function(){});Ext.globalEval=this.execScript?function(t){execScript(t)}:function(t){(function(){eval(t)})()};
Function.prototype.bind||function(){var t=Array.prototype.slice,l=function(l){var u=t.call(arguments,1),q=this;if(u.length)return function(){var n=arguments;return q.apply(l,n.length?u.concat(t.call(n)):u)};u=null;return function(){return q.apply(l,arguments)}};Function.prototype.bind=l;l.$extjs=!0}();Ext=Ext||window.Ext||{};
Ext.Microloader=Ext.Microloader||function(){var t=function(d,m,e){e&&t(d,e);if(d&&m&&"object"==typeof m)for(var k in m)d[k]=m[k];return d},l=Ext.Boot,p=[],u=!1,q={},n={platformTags:q,detectPlatformTags:function(){var d=navigator.userAgent,m=q.isMobile=/Mobile(\/|\s)/.test(d),e,k,l,p;e=document.createElement("div");k="iPhone;iPod;Android;Silk;Android 2;BlackBerry;BB;iPad;RIM Tablet OS;MSIE 10;Trident;Chrome;Tizen;Firefox;Safari;Windows Phone".split(";");var g={};l=k.length;var s;for(s=0;s<l;s++)p=
k[s],g[p]=RegExp(p).test(d);m=g.iPhone||g.iPod||!g.Silk&&g.Android&&(g["Android 2"]||m)||(g.BlackBerry||g.BB)&&g.isMobile||g["Windows Phone"];d=!q.isPhone&&(g.iPad||g.Android||g.Silk||g["RIM Tablet OS"]||g["MSIE 10"]&&/; Touch/.test(d));k="ontouchend"in e;!k&&(e.setAttribute&&e.removeAttribute)&&(e.setAttribute("ontouchend",""),k="function"===typeof e.ontouchend,"undefined"!==typeof e.ontouchend&&(e.ontouchend=void 0),e.removeAttribute("ontouchend"));k=k||navigator.maxTouchPoints||navigator.msMaxTouchPoints;
e=!m&&!d;l=g["MSIE 10"];p=g.Blackberry||g.BB;t(q,n.loadPlatformsParam(),{phone:m,tablet:d,desktop:e,touch:k,ios:g.iPad||g.iPhone||g.iPod,android:g.Android||g.Silk,blackberry:p,safari:g.Safari&&p,chrome:g.Chrome,ie10:l,windows:l||g.Trident,tizen:g.Tizen,firefox:g.Firefox});Ext.beforeLoad&&Ext.beforeLoad(q)},loadPlatformsParam:function(){var d=window.location.search.substr(1).split("\x26"),m={},e,k,l;for(e=0;e<d.length;e++)k=d[e].split("\x3d"),m[k[0]]=k[1];if(m.platformTags){k=m.platform.split(/\W/);
d=k.length;for(e=0;e<d;e++)l=k[e].split(":")}return l},initPlatformTags:function(){n.detectPlatformTags()},getPlatformTags:function(){return n.platformTags},filterPlatform:function(d){d=[].concat(d);var m=n.getPlatformTags(),e,k,l;e=d.length;for(k=0;k<e;k++)if(l=d[k],m.hasOwnProperty(l))return!!m[l];return!1},init:function(){n.initPlatformTags();Ext.filterPlatform=n.filterPlatform},initManifest:function(d){n.init();d=d||Ext.manifest;"string"===typeof d&&(d=l.fetchSync(l.baseUrl+d+".json"),d=JSON.parse(d.content));
return Ext.manifest=d},load:function(d){d=n.initManifest(d);var m=d.loadOrder,e=m?l.createLoadOrderMap(m):null,k=[],p=(d.js||[]).concat(d.css||[]),q,g,s,h,a=function(){u=!0;n.notify()};s=p.length;for(g=0;g<s;g++)q=p[g],h=!0,q.platform&&!n.filterPlatform(q.platform)&&(h=!1),h&&k.push(q.path);m&&(d.loadOrderMap=e);l.load({url:k,loadOrder:m,loadOrderMap:e,sequential:!0,success:a,failure:a})},onMicroloaderReady:function(d){u?d():p.push(d)},notify:function(){for(var d;d=p.shift();)d()}};return n}();
Ext.manifest=Ext.manifest||"bootstrap";Ext.Microloader.load();</script>

</head>
<body style="min-height:100%;background-color:#f5f5f5">
	<div id="wapps_loading" style="margin-top:-70px;position:absolute;top:50%;width:95%;text-align:center;z-index:-999999;">
		<img src="res/images/loading.gif" alt="Loading..." />
		<h3 style="color:#999999" class="farial">Loading...</h3>
	</div>
	<iframe name="bsaving" frameborder="0" width="0" height="0"></iframe>
</body>
</html>
