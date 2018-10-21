this.SimpleGoogleAdsEditor=function(e){var t={};function o(n){if(t[n])return t[n].exports;var r=t[n]={i:n,l:!1,exports:{}};return e[n].call(r.exports,r,r.exports,o),r.l=!0,r.exports}return o.m=e,o.c=t,o.d=function(e,t,n){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(e,t){if(1&t&&(e=o(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)o.d(n,r,function(t){return e[t]}.bind(null,r));return n},o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="",o(o.s=5)}([function(e,t){!function(){e.exports=this.wp.element}()},function(e,t){!function(){e.exports=this.wp.i18n}()},function(e,t){!function(){e.exports=this.wp.blocks}()},function(e,t){!function(){e.exports=this.wp.components}()},function(e,t,o){},function(e,t,o){"use strict";o.r(t);var n=o(0),r=o(1),l=o(2),c=o(3);o(4);Object(l.registerBlockType)("simple-google-ads/ad",{title:Object(r._x)("Ad ","block name","simple-google-ads"),description:Object(r.__)("Display a single Google Ad Manager ad.","simple-google-ads"),icon:Object(n.createElement)("svg",{className:"logo",width:"112px",height:"112px",enableBackground:"new 0 0 192 192",version:"1.1",viewBox:"0 0 192 192",xmlns:"http://www.w3.org/2000/svg"},Object(n.createElement)("rect",{width:"192",height:"192",fill:"none",opacity:".4"}),Object(n.createElement)("linearGradient",{id:"b",x1:"96",x2:"12.922",y1:"25",y2:"108.08",gradientUnits:"userSpaceOnUse"},Object(n.createElement)("stop",{stopColor:"#1A6DDD",offset:"0"}),Object(n.createElement)("stop",{stopColor:"#2976E6",offset:".1393"}),Object(n.createElement)("stop",{stopColor:"#377FEE",offset:".339"}),Object(n.createElement)("stop",{stopColor:"#4084F3",offset:".5838"}),Object(n.createElement)("stop",{stopColor:"#4285F4",offset:"1"})),Object(n.createElement)("path",{d:"M84,13L12.92,84.31c-6.56,6.56-6.56,17.21,0,23.77s17.21,6.56,23.77,0L108,37L84,13z",fill:"url(#b)"}),Object(n.createElement)("path",{d:"m108.01 37c-6.64 6.64-17.39 6.66-24.03 0.02s-6.62-17.39 0.02-24.04c6.64-6.64 17.39-6.66 24.04-0.02s6.62 17.4-0.03 24.04z",fill:"#34A853"}),Object(n.createElement)("path",{d:"m179.09 84.04c-6.55-6.56-17.16-6.56-23.71-0.02l-0.01-0.01-71.37 70.99 23.89 24.11 71.22-71.33-0.01-0.01c6.54-6.55 6.53-17.18-0.01-23.73z",fill:"#FBBC04"}),Object(n.createElement)("circle",{cx:"96",cy:"167",r:"17",fill:"#34A853"}),Object(n.createElement)("path",{d:"m144.02 47.99c-6.64-6.65-17.39-6.65-24.03 0-0.14 0.14-0.26 0.29-0.39 0.44l-35.6 35.57 24 24 35.59-35.55c0.14-0.13 0.29-0.25 0.43-0.39 6.64-6.65 6.64-17.43 0-24.07z",fill:"#FBBC04"}),Object(n.createElement)("linearGradient",{id:"a",x1:"95.93",x2:"47.948",y1:"96.059",y2:"144.04",gradientUnits:"userSpaceOnUse"},Object(n.createElement)("stop",{stopColor:"#1A6DDD",offset:"0"}),Object(n.createElement)("stop",{stopColor:"#2775E5",offset:".1326"}),Object(n.createElement)("stop",{stopColor:"#367EED",offset:".3558"}),Object(n.createElement)("stop",{stopColor:"#3F83F2",offset:".6162"}),Object(n.createElement)("stop",{stopColor:"#4285F4",offset:"1"})),Object(n.createElement)("path",{d:"m83.95 84.07l-36.19 36.15c-6.62 6.6-6.36 17.26 0.25 23.87 6.61 6.6 17.08 6.65 23.69 0.05l36.22-36.06-23.97-24.01z",fill:"url(#a)"}),Object(n.createElement)("path",{d:"m108 108c-6.65 6.65-17.37 6.79-24.02 0.14s-6.63-17.49 0.02-24.14 17.4-6.58 24.05 0.07 6.6 17.29-0.05 23.93z",fill:"#34A853"})),category:"widgets",keywords:[Object(r.__)("ad","simple-google-ads"),Object(r.__)("google","simple-google-ads")],supports:{customClassName:!1,html:!1,multiple:!0},attributes:{tag:{type:"string",default:""}},transforms:{from:[{type:"shortcode",tag:"simple-google-ads-ad-tag",attributes:{tag:{type:"string",shortcode:function(e){return e.id||e.tag}}}}]},edit:function(e){var t=e.className,o=e.attributes.tag,l=e.setAttributes;return Object(n.createElement)("div",{className:t},Object(n.createElement)(c.TextControl,{label:Object(r.__)("Ad tag","simple-google-ads"),help:Object(r.__)("Type the name of the ad tag that you want to display","simple-google-ads"),value:o,onChange:function(e){l({tag:e})}}))},save:function(){return null}})}]);