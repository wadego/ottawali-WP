import{u as E,f as R,m as U}from"./links.da3be5e7.js";import"./default-i18n.3881921e.js";import{u as B,W as H}from"./Wizard.6351b3ab.js";import{C as P,a as V}from"./index.6d5de07f.js";import{r as o,c as _,d as t,w as n,b as v,f as g,o as u,a as s,D as a,z as i,K as D,L as F}from"./vue.runtime.esm-bundler.c297bd08.js";import"./Caret.8cc4e863.js";import{_ as O}from"./_plugin-vue_export-helper.8a32e791.js";import{G as Y,a as G}from"./Row.b4141467.js";import"./constants.44daa6bb.js";/* empty css                                              */import"./TruSeoHighlighter.271256b4.js";import{B as q}from"./Checkbox.1f4414d4.js";import{C as j}from"./Index.e21839d7.js";import{C as K}from"./ProBadge.55f2290c.js";import{C as Q}from"./Tooltip.42b4f815.js";import{W as J,a as X,b as Z}from"./Header.0920a349.js";import{W as ee,_ as te}from"./Steps.8dc7c182.js";import"./isArrayLikeObject.10b615a9.js";import"./addons.1640e0f5.js";import"./upperFirst.d65414ba.js";import"./_stringToArray.a4422725.js";/* empty css                                            */import"./postContent.d84eb650.js";import"./cleanForSlug.a67f7e84.js";import"./Ellipse.404f2a7a.js";import"./toFinite.c2274946.js";import"./Checkmark.dcb95692.js";/* empty css                                              */import"./Logo.be6331d8.js";const se={setup(){const{strings:r}=B();return{rootStore:E(),setupWizardStore:R(),composableStrings:r}},components:{BaseCheckbox:q,CoreAlert:P,CoreModal:j,CoreProBadge:K,CoreTooltip:Q,GridColumn:Y,GridRow:G,SvgCircleQuestionMark:V,WizardBody:J,WizardCloseAndExit:ee,WizardContainer:X,WizardHeader:Z,WizardSteps:te},mixins:[H],data(){return{loading:!1,stage:"smart-recommendations",showModal:!1,loadingModal:!1,strings:U(this.composableStrings,{setupSiteAnalyzer:this.$t.__("Setup Site Analyzer + Smart Recommendations",this.$td),description:this.$t.sprintf(this.$t.__("Get helpful suggestions from %1$s on how to optimize your website content, so you can rank higher in search results.",this.$td),"AIOSEO"),yourEmailAddress:this.$t.__("Your Email Address",this.$td),yourEmailIsNeeded:this.$t.__("Your email is needed so you can receive SEO recommendations. This email will also be used to connect your site with our SEO API.",this.$td),helpMakeAioseoBetter:this.$t.sprintf(this.$t.__("Help make %1$s better for everyone",this.$td),"AIOSEO"),yesCountMeIn:this.$t.__("Yes, count me in",this.$td),wouldYouLikeToPurchase:this.$t.__("Would you like to purchase and install the following features now?",this.$td),theseFeaturesAreAvailable:this.$t.__("An upgrade is required to unlock the following features.",this.$td),youWontHaveAccess:this.$t.__("You won't have access to this functionality until the extensions have been purchased and installed.",this.$td),illDoItLater:this.$t.__("I'll do it later",this.$td),purchaseAndInstallNow:this.$t.__("Purchase and Install Now",this.$td),bonusText:this.$t.sprintf(this.$t.__("%1$sBonus:%2$s You can upgrade your plan today and %3$ssave %4$s off%5$s (discount auto-applied).",this.$td),"<strong>","</strong>","<strong>",this.$constants.DISCOUNT_PERCENTAGE,"</strong>"),usageTrackingTooltip:this.$t.sprintf(this.$t.__("Complete documentation on usage tracking is available %1$shere%2$s.",this.$td),this.$t.sprintf('<strong><a href="%1$s" target="_blank">',this.$links.getDocUrl("usageTracking")),"</a></strong>")})}},computed:{selectedFeaturesNeedsUpsell(){let r=!1;return this.setupWizardStore.features.forEach(l=>{this.needsUpsell(this.features.find(f=>f.value===l))&&(r=!0)}),r}},methods:{purchase(){const r=`&license-redirect=${btoa(this.rootStore.aioseo.urls.aio.wizard)}#/license-key`;window.open("https://aioseo.com/pricing/?features[]="+this.getSelectedUpsellFeatures.map(l=>l.value).join("&features[]=")+r),this.$router.push(this.setupWizardStore.getNextLink)},saveAndContinue(){this.loading=!0,this.setupWizardStore.saveWizard("smartRecommendations").then(()=>{if(!this.selectedFeaturesNeedsUpsell)return this.$router.push(this.setupWizardStore.getNextLink);this.showModal=!0,this.loading=!1})},skipStep(){this.setupWizardStore.saveWizard(),this.$router.push(this.setupWizardStore.getNextLink)},preventUncheck(r){r.preventDefault(),r.stopPropagation()}},mounted(){this.setupWizardStore.smartRecommendations.accountInfo=this.rootStore.aioseo.user.data.data.user_email}},oe={class:"aioseo-wizard-smart-recommendations"},ne={class:"header"},ie={class:"description"},re={class:"aioseo-settings-row no-border small-padding"},ae={class:"settings-name"},le={class:"name small-margin"},de={class:"aioseo-description"},ce={key:0,class:"aioseo-settings-row no-border no-margin small-padding"},ue={class:"settings-name"},me={class:"name small-margin"},_e=["innerHTML"],pe={class:"go-back"},he=s("div",{class:"spacer"},null,-1),ge={class:"aioseo-modal-body"},fe=["innerHTML"],ke={class:"settings-name"},ve={class:"name small-margin"},ye={class:"aioseo-description-text"},ze=["innerHTML"],Se={class:"actions"},be=s("div",{class:"spacer"},null,-1),we={class:"go-back"};function Ce(r,l,f,c,e,m){const y=o("wizard-header"),z=o("wizard-steps"),S=o("base-input"),b=o("svg-circle-question-mark"),w=o("core-tooltip"),C=o("base-toggle"),p=o("router-link"),h=o("base-button"),W=o("wizard-body"),A=o("wizard-close-and-exit"),T=o("wizard-container"),x=o("core-pro-badge"),k=o("grid-column"),$=o("base-checkbox"),L=o("grid-row"),M=o("core-alert"),I=o("core-modal");return u(),_("div",oe,[t(y),t(T,null,{default:n(()=>[t(W,null,{footer:n(()=>[s("div",pe,[t(p,{to:c.setupWizardStore.getPrevLink,class:"no-underline"},{default:n(()=>[a("←")]),_:1},8,["to"]),a("   "),t(p,{to:c.setupWizardStore.getPrevLink},{default:n(()=>[a(i(e.strings.goBack),1)]),_:1},8,["to"])]),he,t(h,{type:"gray",onClick:m.skipStep},{default:n(()=>[a(i(e.strings.skipThisStep),1)]),_:1},8,["onClick"]),t(h,{type:"blue",loading:e.loading,onClick:m.saveAndContinue},{default:n(()=>[a(i(e.strings.saveAndContinue)+" →",1)]),_:1},8,["loading","onClick"])]),default:n(()=>[t(z),s("div",ne,i(e.strings.setupSiteAnalyzer),1),s("div",ie,i(e.strings.description),1),s("div",re,[s("div",ae,[s("div",le,i(e.strings.yourEmailAddress),1)]),t(S,{size:"medium",modelValue:c.setupWizardStore.smartRecommendations.accountInfo,"onUpdate:modelValue":l[0]||(l[0]=d=>c.setupWizardStore.smartRecommendations.accountInfo=d)},null,8,["modelValue"]),s("div",de,i(e.strings.yourEmailIsNeeded),1)]),r.$isPro?g("",!0):(u(),_("div",ce,[s("div",ue,[s("div",me,[a(i(e.strings.helpMakeAioseoBetter)+" ",1),t(w,null,{tooltip:n(()=>[s("div",{innerHTML:e.strings.usageTrackingTooltip},null,8,_e)]),default:n(()=>[t(b)]),_:1})])]),t(C,{modelValue:c.setupWizardStore.smartRecommendations.usageTracking,"onUpdate:modelValue":l[1]||(l[1]=d=>c.setupWizardStore.smartRecommendations.usageTracking=d)},{default:n(()=>[a(i(e.strings.yesCountMeIn),1)]),_:1},8,["modelValue"])]))]),_:1}),t(A)]),_:1}),e.showModal?(u(),v(I,{key:0,onClose:l[2]||(l[2]=d=>e.showModal=!1)},{headerTitle:n(()=>[a(i(e.strings.wouldYouLikeToPurchase),1)]),body:n(()=>[s("div",ge,[s("div",{class:"available-features",innerHTML:e.strings.theseFeaturesAreAvailable},null,8,fe),(u(!0),_(D,null,F(r.getSelectedUpsellFeatures,(d,N)=>(u(),_("div",{key:N,class:"aioseo-settings-row feature-grid small-padding medium-margin"},[t(L,null,{default:n(()=>[t(k,{xs:"11"},{default:n(()=>[s("div",ke,[s("div",ve,[a(i(d.name)+" ",1),r.needsUpsell(d)?(u(),v(x,{key:0})):g("",!0)]),s("div",ye,i(d.description),1)])]),_:2},1024),t(k,{xs:"1"},{default:n(()=>[t($,{round:"",class:"no-clicks",type:"green",modelValue:!0,onClick:m.preventUncheck},null,8,["onClick"])]),_:1})]),_:2},1024)]))),128)),s("div",{class:"available-features no-access",innerHTML:e.strings.youWontHaveAccess},null,8,ze),s("div",Se,[be,s("div",we,[t(p,{to:c.setupWizardStore.getNextLink},{default:n(()=>[a(i(e.strings.illDoItLater),1)]),_:1},8,["to"])]),t(h,{type:"green",loading:e.loadingModal,onClick:m.purchase},{default:n(()=>[a(i(e.strings.purchaseAndInstallNow),1)]),_:1},8,["loading","onClick"])]),t(M,{type:"yellow",innerHTML:e.strings.bonusText},null,8,["innerHTML"])])]),_:1})):g("",!0)])}const tt=O(se,[["render",Ce]]);export{tt as default};
