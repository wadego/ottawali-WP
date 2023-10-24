import{o,c as n,a as r,n as f,r as l,b as d,f as c,z as u}from"./vue.runtime.esm-bundler.c297bd08.js";import{_ as i}from"./_plugin-vue_export-helper.8a32e791.js";const y={props:{scoreColor:String,score:{type:Number,required:!0},strokeWidth:{type:Number,default(){return 2}}},computed:{getClass(){let t="",s="";switch(!0){case 33>=this.score:t="fast",s="stroke-red";break;case 66>=this.score:t="medium",s="stroke-orange";break;default:t="slow",s="stroke-green"}return this.scoreColor!==void 0&&(s=`stroke-${this.scoreColor}`),`${t} ${s}`}}},m={class:"aioseo-seo-site-score-svg",viewBox:"0 0 34 34",xmlns:"http://www.w3.org/2000/svg"},v=["stroke-width","r"],p=["stroke-width","stroke-dasharray","r"];function x(t,s,e,g,a,_){return o(),n("svg",m,[r("circle",{class:"aioseo-seo-site-score__background","stroke-width":e.strokeWidth,fill:"none",cx:"17",cy:"17",r:17-e.strokeWidth/2},null,8,v),r("circle",{class:f(["aioseo-seo-site-score__circle",_.getClass]),"stroke-width":e.strokeWidth,"stroke-dasharray":`${e.score},100`,"stroke-linecap":"round",fill:"none",cx:"17",cy:"17",r:17-e.strokeWidth/2},null,10,p)])}const w=i(y,[["render",x]]);const S={},C={class:"aioseo-seo-site-score-svg-loading",viewBox:"0 0 33.83098862 33.83098862",xmlns:"http://www.w3.org/2000/svg"},W=r("circle",{fill:"none",class:"aioseo-seo-site-score-loading__circle",cx:"16.91549431",cy:"16.91549431",r:"15.91549431","stroke-linecap":"round","stroke-width":"2","stroke-dasharray":"93","stroke-dashoffset":"90"},null,-1),$=[W];function b(t,s){return o(),n("svg",C,$)}const B=i(S,[["render",b]]);const z={components:{SvgSeoSiteScore:w,SvgSeoSiteScoreLoading:B},props:{score:Number,loading:Boolean,description:String,strokeWidth:{type:Number,default(){return 1.75}}},data(){return{strings:{analyzing:this.$t.__("Analyzing...",this.$td)}}}},L={class:"aioseo-site-score"},N={class:"aioseo-score-amount-wrapper"},A={key:0,class:"aioseo-score-amount"},H={class:"score"},I=r("span",{class:"total"},"/ 100",-1),M=["innerHTML"],T={key:2,class:"score-analyzing"};function V(t,s,e,g,a,_){const h=l("svg-seo-site-score-loading"),k=l("svg-seo-site-score");return o(),n("div",L,[e.loading?(o(),d(h,{key:0})):c("",!0),e.loading?c("",!0):(o(),d(k,{key:1,score:e.score,strokeWidth:e.strokeWidth},null,8,["score","strokeWidth"])),r("div",N,[e.loading?c("",!0):(o(),n("div",A,[r("span",H,u(e.score),1),I])),e.loading?c("",!0):(o(),n("div",{key:1,class:"score-description",innerHTML:e.description},null,8,M)),e.loading?(o(),n("div",T,u(a.strings.analyzing),1)):c("",!0)])])}const E=i(z,[["render",V]]);export{E as C,w as S};