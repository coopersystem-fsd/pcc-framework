!function(e){var t={};function r(c){if(t[c])return t[c].exports;var n=t[c]={i:c,l:!1,exports:{}};return e[c].call(n.exports,n,n.exports,r),n.l=!0,n.exports}r.m=e,r.c=t,r.d=function(e,t,c){r.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:c})},r.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},r.t=function(e,t){if(1&t&&(e=r(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var c=Object.create(null);if(r.r(c),Object.defineProperty(c,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var n in e)r.d(c,n,function(t){return e[t]}.bind(null,n));return c},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,"a",t),t},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},r.p="",r(r.s=9)}([function(e,t){!function(){e.exports=this.wp.element}()},function(e,t){!function(){e.exports=this.wp.blocks}()},function(e,t){!function(){e.exports=this.wp.domReady}()},function(e,t){!function(){e.exports=this.wp.i18n}()},function(e,t){!function(){e.exports=this.wp.editPost}()},function(e,t){e.exports=function(e,t,r){return t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}},function(e,t){!function(){e.exports=this.lodash}()},function(e,t){!function(){e.exports=this.wp.hooks}()},function(e,t){var r;(r=jQuery)("input[name=pcc_event_oc_event_link]").change((function(e){var t=e.target.value;if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(t)){var c=(t=t.replace("https://opencollective.com/","")).split("/");if(c.indexOf("events")>-1){var n=c[c.indexOf("events")+1],o="https://opencollective.com/".concat(c[0],"/events/").concat(n,".json");r.getJSON(o,(function(e){"id"in e&&r("input[name=pcc_event_oc_event_id]").val(e.id)}))}}}))},function(e,t,r){"use strict";r.r(t);var c=r(5),n=r.n(c),o=r(0),a=r(6),l=wp.i18n.__,i=wp.data.withSelect,s=wp.components,u=s.SelectControl,p=s.Placeholder,b=s.Spinner,f={value:"0",label:l("-- Select Page --","pcc-framework")},m=i((function(e){return{pages:e("core").getEntityRecords("postType","page",{per_page:-1,orderby:"menu_order"})}}))((function(e){var t,r=e.pages,c=e.attributes,n=e.setAttributes,i=e.className,s=c.parent;return r?r&&0===r.length?Object(o.createElement)("div",{className:i},Object(o.createElement)("p",null,Object(o.createElement)("em",null,l("No pages found.","pcc-framework"))),";"):Object(o.createElement)("div",{className:i},Object(o.createElement)(u,{label:l("Parent Page","pcc-framework"),value:s,options:(t=[],Object(a.isUndefined)(r)||(t=r.map((function(e){var t=e.id,r=e.title.rendered;return{value:t,label:""===r?"".concat(t," : ").concat(l("No page title","pcc-framework")):r}}))),t.unshift(f),t),onChange:function(e){n({parent:Number(e)})}})):Object(o.createElement)(p,null,Object(o.createElement)(b,null))}));function d(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var c=Object.getOwnPropertySymbols(e);t&&(c=c.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,c)}return r}var k=wp.i18n.__,g=wp.blocks.registerBlockType,w=wp.serverSideRender;g("pcc/child-pages",{title:"Child Pages",description:"Generate a list of child pages in various formats",icon:"networking",category:"blocks",attributes:{exclude:{type:"integer"},parent:{type:"integer"}},styles:[{name:"card",label:k("Card","pcc-framework")},{name:"card-with-excerpt",label:k("Card with Excerpt","pcc-framework"),isDefault:!0},{name:"text-only",label:k("Text Only","pcc-framework")}],edit:function(e){var t=e.attributes;return e.isSelected?Object(o.createElement)(m,function(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?d(Object(r),!0).forEach((function(t){n()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):d(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}({},e)):Object(o.createElement)(w,{block:"pcc/child-pages",attributes:t})},save:function(){return null}});var v=wp.i18n.__,y=wp.blocks.registerBlockType,O=wp.components.TextControl,h=y("pcc/participants-button",{title:"Participants",description:"Link to an event’s participants page",icon:"groups",category:"blocks",attributes:{label:{default:v("View all participants","pcc-framework"),type:"string"}},styles:[{name:"default",label:v("Default","pcc-framework"),isDefault:!0}],edit:function(e){var t=e.attributes,r=e.setAttributes;return e.isSelected?Object(o.createElement)("div",{className:"wp-block-button"},Object(o.createElement)(O,{label:v("Label","pcc-framework"),value:t.label,onChange:function(e){r({label:e||h.attributes.label.default})}})):Object(o.createElement)("div",{className:"wp-block-button"},Object(o.createElement)("a",{className:"wp-block-button__link",href:"participants"},t.label))},save:function(){return null}}),j=wp.i18n.__,_=wp.blocks.registerBlockType,E=wp.components.TextControl,x=_("pcc/program-button",{title:"Program",description:"Link to an event’s program page",icon:"calendar",category:"blocks",attributes:{label:{default:j("See the full program","pcc-framework"),type:"string"}},styles:[{name:"default",label:j("Default","pcc-framework"),isDefault:!0}],edit:function(e){var t=e.attributes,r=e.setAttributes;return e.isSelected?Object(o.createElement)("div",{className:"wp-block-button"},Object(o.createElement)(E,{label:j("Label","pcc-framework"),value:t.label,onChange:function(e){r({label:e||x.attributes.label.default})}})):Object(o.createElement)("div",{className:"wp-block-button"},Object(o.createElement)("a",{className:"wp-block-button__link",href:"program"},t.label))},save:function(){return null}}),P=wp.i18n.__,S=wp.blocks.registerBlockType,N=wp.serverSideRender;S("pcc/recent-content",{title:P("Recent Content","pcc-framework"),description:P("Generate a grid of recent content from various sources","pcc-framework"),icon:"screenoptions",category:"blocks",edit:function(){return Object(o.createElement)(N,{block:"pcc/recent-content"})},save:function(){return null}});var T=wp.i18n.__,C=wp.blocks.registerBlockType,B=wp.serverSideRender;C("pcc/projects",{title:T("Projects","pcc-framework"),description:T("Generate a content grid of available projects","pcc-framework"),icon:"screenoptions",category:"blocks",edit:function(){return Object(o.createElement)(B,{block:"pcc/projects"})},save:function(){return null}});var D,L,A=wp.i18n.__,M=wp.blocks.registerBlockType,R=wp.components,V=R.Path,G=R.SVG,H=R.TextControl,Z=M("pcc/social-links",{title:"Social Links",description:"Links to Facebook and Twitter",icon:"share",category:"blocks",attributes:{label_facebook:{default:A("Platform Cooperativism – Discussion & Linkshare","pcc-framework"),type:"string"},label_twitter:{default:A("Platform Co-op Development Kit","pcc-framework"),type:"string"}},styles:[{name:"default",label:A("Default","pcc-framework"),isDefault:!0},{name:"icon-only",label:A("Icon Only","pcc-framework")}],edit:function(e){var t=e.attributes,r=e.setAttributes,c=e.className,n=e.isSelected,a=[{label:t.label_facebook,url:"#",icon:Object(o.createElement)(G,{className:"social-links__icon",width:"35",height:"35",viewBox:"0 0 35 35",xmlns:"http://www.w3.org/2000/svg"},Object(o.createElement)(V,{fill:"currentColor",transform:"translate(-3 -4)",d:"M18.03,31.02h3.934V21.5h2.624l.348-3.281H21.964l0-1.643c0-.855.082-1.314,1.309-1.314h1.64V11.98H22.292c-3.153,0-4.262,1.592-4.262,4.268v1.97H16.064V21.5H18.03ZM20.5,39A17.5,17.5,0,1,1,38,21.5,17.5,17.5,0,0,1,20.5,39Z"}))},{label:t.label_twitter,url:"#",icon:Object(o.createElement)(G,{className:"social-links__icon",width:"35",height:"35",viewBox:"0 0 35 35",xmlns:"http://www.w3.org/2000/svg"},Object(o.createElement)(V,{fill:"currentColor",transform:"translate(-3.723 -5.157)",d:"M21.224,5.157a17.5,17.5,0,1,0,17.5,17.5A17.5,17.5,0,0,0,21.224,5.157Zm8.815,13.972c.009.189.013.379.013.571A12.544,12.544,0,0,1,10.743,30.267a9.019,9.019,0,0,0,1.052.061,8.848,8.848,0,0,0,5.477-1.888,4.417,4.417,0,0,1-4.119-3.064,4.307,4.307,0,0,0,.829.079,4.383,4.383,0,0,0,1.162-.154,4.414,4.414,0,0,1-3.539-4.324c0-.019,0-.038,0-.057a4.4,4.4,0,0,0,2,.552,4.416,4.416,0,0,1-1.365-5.889,12.521,12.521,0,0,0,9.091,4.607,4.413,4.413,0,0,1,7.515-4.022,8.809,8.809,0,0,0,2.8-1.07,4.423,4.423,0,0,1-1.94,2.44,8.817,8.817,0,0,0,2.534-.694A8.91,8.91,0,0,1,30.039,19.129Z"}))}];return n?Object(o.createElement)("div",{className:c},Object(o.createElement)(H,{label:A("Facebook Label","pcc-framework"),value:t.label_facebook,onChange:function(e){r({label_facebook:e||Z.attributes.label_facebook.default})}}),Object(o.createElement)(H,{label:A("Twitter Label","pcc-framework"),value:t.label_twitter,onChange:function(e){r({label_twitter:e||Z.attributes.label_twitter.default})}})):Object(o.createElement)("ul",{className:c},a.map((function(e,t){return Object(o.createElement)("li",{key:t,className:"social-links__item"},Object(o.createElement)("a",{className:"social-links__link",rel:"external",href:e.url},e.icon,Object(o.createElement)("span",{className:"social-links__label"},e.label)))})))},save:function(){return null}}),q=r(3),z=r(7),F=(r(4),r(2)),I=r.n(F),J=r(1);D=["pcc/child-pages","pcc/participants-button","pcc/program-button","pcc/recent-content","pcc/projects","pcc/social-links","core/paragraph","core/image","core/heading","core/list","core/quote","core/shortcode","core/button","core/columns","core/column","core/embed","core-embed/twitter","core-embed/youtube","core-embed/facebook","core-embed/instagram","core-embed/soundcloud","core-embed/flickr","core-embed/vimeo","core-embed/meetup-com","core-embed/polldaddy","core-embed/scribd","core-embed/slideshare","core-embed/ted","core/group","core/freeform","core/media-text","core/missing","core/search","core/block","core/subhead","core/text-columns"],I()((function(){Object(J.getBlockTypes)().forEach((function(e){var t=e.name;return-1===D.indexOf(t)&&Object(J.unregisterBlockType)(t)}))})),function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:[];Object(z.addFilter)("blocks.registerBlockType","sage/inserter",(function(r){return r.category=t.includes(r.category)?r.category:r.category=e,r}))}("blocks"),L=[{block:"core/button",styles:["outline","fill"]},{block:"core/image",styles:["default","circle-mask"]},{block:"core/pullquote",styles:["default","solid-color"]},{block:"core/table",styles:["regular","stripes"]},{block:"core/quote",styles:["default","large"]}],I()((function(){return L.forEach((function(e){var t=e.block;return e.styles.forEach((function(e){return Object(J.unregisterBlockStyle)(t,e)}))}))})),function(e){I()((function(){return e.forEach((function(e){var t=e.block;return e.styles.forEach((function(e){return Object(J.registerBlockStyle)(t,e)}))}))}))}([{block:"core/button",styles:[{name:"solid",label:Object(q.__)("Solid","pcc-framework")},{name:"outline",label:Object(q.__)("Outline","pcc-framework")}]}]);r(8)}]);