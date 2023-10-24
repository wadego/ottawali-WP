import{r as i,_ as $,a as g,j as t,f as V,l as x,ax as O,a6 as Z,a5 as K,a7 as tt,ay as st,C as at,az as et,S as ot}from"./main.js";import{u as nt,v as rt,a as A,S as _,F as it,R as dt}from"./MetaData-476729d9.js";import ct from"./Alert-f7758b2a.js";import{a as lt,u as ut,i as pt}from"./Actions-9a089581.js";import{t as ft,v as mt,B as h}from"./DockRight-70b6fcac.js";import{M as gt,I as j}from"./IconButton-18a73260.js";import{g as b,a as L,b as N,f as k,c as B,h as M}from"./Typography-c1919a6b.js";function xt(s){return b("MuiCard",s)}L("MuiCard",["root"]);const Ct=["className","raised"],ht=s=>{const{classes:a}=s;return M({root:["root"]},xt,a)},jt=N(gt,{name:"MuiCard",slot:"Root",overridesResolver:(s,a)=>a.root})(()=>({overflow:"hidden"})),Rt=i.forwardRef(function(a,r){const e=k({props:a,name:"MuiCard"}),{className:d,raised:l=!1}=e,u=$(e,Ct),p=g({},e,{raised:l}),f=ht(p);return t.jsx(jt,g({className:B(f.root,d),elevation:l?8:void 0,ref:r,ownerState:p},u))}),wt=Rt;function yt(s){return b("MuiCardActions",s)}L("MuiCardActions",["root","spacing"]);const St=["disableSpacing","className"],vt=s=>{const{classes:a,disableSpacing:r}=s;return M({root:["root",!r&&"spacing"]},yt,a)},At=N("div",{name:"MuiCardActions",slot:"Root",overridesResolver:(s,a)=>{const{ownerState:r}=s;return[a.root,!r.disableSpacing&&a.spacing]}})(({ownerState:s})=>g({display:"flex",alignItems:"center",padding:8},!s.disableSpacing&&{"& > :not(:first-of-type)":{marginLeft:8}})),_t=i.forwardRef(function(a,r){const e=k({props:a,name:"MuiCardActions"}),{disableSpacing:d=!1,className:l}=e,u=$(e,St),p=g({},e,{disableSpacing:d}),f=vt(p);return t.jsx(At,g({className:B(f.root,l),ownerState:p,ref:r},u))}),$t=_t;function bt(s){return b("MuiCardContent",s)}L("MuiCardContent",["root"]);const Lt=["className","component"],Nt=s=>{const{classes:a}=s;return M({root:["root"]},bt,a)},kt=N("div",{name:"MuiCardContent",slot:"Root",overridesResolver:(s,a)=>a.root})(()=>({padding:16,"&:last-child":{paddingBottom:24}})),Bt=i.forwardRef(function(a,r){const e=k({props:a,name:"MuiCardContent"}),{className:d,component:l="div"}=e,u=$(e,Lt),p=g({},e,{component:l}),f=Nt(p);return t.jsx(kt,g({as:l,className:B(f.root,d),ownerState:p,ref:r},u))}),Mt=Bt;const Et=i.lazy(()=>V(()=>import("./Row-e75550d7.js"),["./Row-e75550d7.js","./main.js","./main-b5300c26.css","./MetaData-476729d9.js","./Typography-c1919a6b.js","./Row-44b4d6d7.css"],import.meta.url)),Ft=i.lazy(()=>V(()=>import("./DebugInfo-78465f89.js"),["./DebugInfo-78465f89.js","./main.js","./main-b5300c26.css","./MetaData-476729d9.js","./Typography-c1919a6b.js"],import.meta.url)),Ot=({formId:s,dbs:a,tbl:r,metaData:e,primaryKey:d,formMode:l,showTable:u,showForm:p})=>{x.debug(a,r,e,d,l);const f=nt(),E=i.useMemo(()=>rt(),[]),[W,F]=i.useState(!1),[P,U]=i.useState(null),[R,w]=i.useState(null),[G,D]=i.useState(!1),[y,T]=i.useState(!1);i.useEffect(()=>{d===null?q():Y()},[d]);const Y=()=>{lt(a,r,d,function(o){x.debug(o),f(O({formId:s,data:o})),U(o),F(o!==null)})},q=()=>{if(e.columns&&Array.isArray(e.columns)){const o={};for(let m=0;m<e.columns.length;m++)o[e.columns[m].column_name]=null;x.debug(o),f(O({formId:s,data:o})),x.debug("init"),U(o),F(!0)}},S=o=>{const m=et(ot.getState(),s);d!==null?ut(a,r,d,m,function(n){n&&n.data&&n.data.data&&n.data.code&&n.data.code.toLowerCase()==="ok"?n.data.data==="Row successfully updated"?(D(!0),w({severity:"success",message:n.data.data}),o&&u(!0)):n.data.data==="Nothing to update"&&(w({severity:"info",message:n.data.data}),o&&u(G)):x.error(n)}):pt(a,r,m,function(n){if(n&&n.data&&n.data.data&&n.data.code&&n.data.code.toLowerCase()==="ok"){if(n.data.data==="Row successfully inserted"){D(!0),w({severity:"success",message:n.data.data}),o&&u(!0);const z={};for(let C=0;C<e.primary_key.length;C++)z[e.primary_key[C].name]=m.columns[e.primary_key[C].name];p(z,dt.UPDATE)}}else x.error(n)})},H=()=>{S(!1)},J=()=>{S(!0)},v=()=>{u(G)},Q=A(o=>Z(o,s));let I="";Q&&(I+=" yd-form-grid");const X=A(o=>K(o,s)),c=A(o=>tt(o,s));return t.jsx(t.Fragment,{children:t.jsx("div",{className:`${s}_wrapper yd-form`,children:W&&P!==null?t.jsxs(t.Fragment,{children:[t.jsx(ft,{position:"static",sx:{marginBottom:`${c.betweenContainers}px`,borderRadius:"4px"},children:t.jsxs(mt,{sx:{backgroundColor:"#fff",justifyContent:"space-between",borderRadius:"4px",paddingLeft:`${c.aroundGridLeft}px`,paddingRight:`${c.aroundGridRight}px`,"@media (min-width: 600px)":{paddingLeft:`${c.aroundGridLeft}px`,paddingRight:`${c.aroundGridRight}px`}},children:[t.jsx(j,{onClick:v,color:"primary",children:t.jsx("i",{className:"fa-solid fa-angles-left"})}),t.jsxs("span",{children:[!y&&t.jsx(j,{color:"primary",onClick:()=>{T(!0)},children:t.jsx("i",{className:"fa-solid fa-bug"})}),y&&t.jsx(j,{color:"primary",onClick:()=>{T(!1)},children:t.jsx("i",{className:"fa-solid fa-bug-slash"})}),t.jsx(j,{color:"primary",className:"setting",onClick:()=>{f(st({}))},children:t.jsx("i",{className:"fa-solid fa-gear"})})]})]})}),y&&at.appDebug&&t.jsx("div",{style:{marginBottom:`${c.betweenContainers}px`},children:t.jsx(i.Suspense,{fallback:t.jsx(_,{}),children:t.jsx(Ft,{formId:s})})}),R!==null&&t.jsx(ct,{severity:R.severity,message:R.message,now:Date.now().toString()}),t.jsxs(wt,{sx:{paddingTop:`${c.aroundGridTop}px`,paddingBottom:`${c.aroundGridTop}px`,paddingLeft:`${c.aroundGridLeft}px`,paddingRight:`${c.aroundGridRight}px`},children:[t.jsx(Mt,{sx:{padding:0},children:t.jsx("form",{id:E,className:`yd-form-content ${I}`,style:{gridTemplateColumns:`repeat(${X}, 1fr)`,gap:`${c.betweenCells}px`,marginBottom:0},onSubmit:o=>(S(!1),o.preventDefault(),!1),children:t.jsx(i.Suspense,{fallback:t.jsx(_,{}),children:t.jsx(Et,{formId:s,dbs:a,tbl:r,metaData:e,data:P,primaryKey:d,formMode:l})})})}),t.jsx($t,{sx:{marginTop:`${c.betweenCells}px`,padding:0,paddingRight:`${c.cellRight}px`,justifyContent:"flex-end"},children:l===it.EDIT?t.jsxs(t.Fragment,{children:[t.jsx(h,{variant:"contained",onClick:H,type:"submit",form:E,children:"APPLY"}),t.jsx(h,{variant:"contained",onClick:J,sx:{marginLeft:"16px"},children:"OK"}),t.jsx(h,{variant:"contained",onClick:v,sx:{marginLeft:"16px"},children:"CANCEL"})]}):t.jsx(h,{variant:"contained",onClick:v,children:"BACK"})})]})]}):t.jsx("div",{style:{padding:"50px"},children:t.jsx(_,{title:"Loading data..."})})})})};export{Ot as default};
