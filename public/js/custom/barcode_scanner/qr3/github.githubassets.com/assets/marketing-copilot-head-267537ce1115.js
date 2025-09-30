"use strict";(globalThis.webpackChunk=globalThis.webpackChunk||[]).push([["marketing-copilot-head"],{70245:(e,t,i)=>{function n(e,t,i){return Math.max(0,Math.min(1,(i-e)/(t-e)))}var s,a,r,o=i(99477);let l=class Common{init(e,t){this.clock=new o.SUY,this.canvasWrapper=e,this.pixelRatio=Math.min(window.devicePixelRatio,2),this.renderer=new o.CP7({canvas:t,antialias:!0,alpha:!0}),this.renderer.setPixelRatio(this.pixelRatio),this.renderer.outputEncoding=o.knz,this.renderer.toneMapping=o.LY2,this.renderer.toneMappingExposure=1.5,this.renderer.setClearColor(16777215,0),this.resize()}resize(){let e=this.canvasWrapper.getBoundingClientRect(),t=e.width,i=e.height;this.aspect=t/i,this.sizes.set(t,i),this.wrapperOffset.set(e.left,e.top),this.camera.aspect=this.aspect,this.camera.updateProjectionMatrix(),this.renderer.setSize(t,i)}getEase(e){return Math.min(1,this.delta*e)}update(){let e=this.clock.getDelta();this.delta=e,this.time+=this.delta}constructor(){this.sizes=new o.FM8,this.pixelRatio=1,this.aspect=1,this.scene=new o.xsS,this.camera=new o.cPb(45,this.aspect,.1,200),this.camera.position.set(0,0,11),this.scene.add(this.camera),this.wrapperOffset=new o.FM8,this.delta=0,this.time=0}},u=new l,c=class Controls{getRandomAnim(e){let t=Math.floor(Math.random()*this.frequences.length),i=this.frequences[t];return e&&"wink"===i&&(i="jamp1"),i}init(){}constructor(){this.params={clickAction:"random",blinkingSpeed:2},this.frequences=["jump1","jump1","jump3","jump3","wink","wink","shake","shake"]}},h=new c,m=class MouseMng{init(){window.addEventListener("mousemove",e=>{let t=(e.clientX-u.wrapperOffset.x)/u.sizes.x;t=(t-.5)*2;let i=(e.clientY-u.wrapperOffset.y)/u.sizes.y;i=(.5-i)*2,this.updateMousePos(t,i)}),window.addEventListener("touchstart",e=>{if(e.touches[0]){let t=(e.touches[0].clientX-u.wrapperOffset.x)/u.sizes.x;t=(t-.5)*2;let i=(e.touches[0].clientY-u.wrapperOffset.y)/u.sizes.y;i=(.5-i)*2,this.updateMousePos(t,i)}})}updateMousePos(e,t){for(let i of(this.pos.target.set(e,t),this.mousemoveFuncs))i()}addMousemoveFunc(e){this.mousemoveFuncs.push(e)}resize(){}update(){this.pos.current.lerp(this.pos.target,u.getEase(2)),this.pos.current2.lerp(this.pos.target,u.getEase(1.5))}constructor(){this.originalPos=new o.FM8,this.mousemoveFuncs=[],this.pos={target:new o.FM8(-3,-3),current:new o.FM8(-3,-3),current2:new o.FM8(-3,-3)}}},d=new m,p={linear:function(e){return e},easeInSine:function(e){return -1*Math.cos(e*(Math.PI/2))+1},easeOutSine:function(e){return Math.sin(e*(Math.PI/2))},easeInOutSine:function(e){return -.5*(Math.cos(Math.PI*e)-1)},easeInQuad:function(e){return e*e},easeOutQuad:function(e){return e*(2-e)},easeInOutQuad:function(e){return e<.5?2*e*e:-1+(4-2*e)*e},easeInCubic:function(e){return e*e*e},easeOutCubic:function(e){let t=e-1;return t*t*t+1},easeInOutCubic:function(e){return e<.5?4*e*e*e:(e-1)*(2*e-2)*(2*e-2)+1},easeInQuart:function(e){return e*e*e*e},easeOutQuart:function(e){let t=e-1;return 1-t*t*t*t},easeInOutQuart:function(e){let t=e-1;return e<.5?8*e*e*e*e:1-8*t*t*t*t},easeInQuint:function(e){return e*e*e*e*e},easeOutQuint:function(e){let t=e-1;return 1+t*t*t*t*t},easeInOutQuint:function(e){let t=e-1;return e<.5?16*e*e*e*e*e:1+16*t*t*t*t*t},easeInExpo:function(e){return 0===e?0:Math.pow(2,10*(e-1))},easeOutExpo:function(e){return 1===e?1:-Math.pow(2,-10*e)+1},easeInOutExpo:function(e){if(0===e||1===e)return e;let t=2*e,i=t-1;return t<1?.5*Math.pow(2,10*i):.5*(-Math.pow(2,-10*i)+2)},easeInCirc:function(e){return -1*(Math.sqrt(1-e/1*e)-1)},easeOutCirc:function(e){let t=e-1;return Math.sqrt(1-t*t)},easeInOutCirc:function(e){let t=2*e,i=t-2;return t<1?-.5*(Math.sqrt(1-t*t)-1):.5*(Math.sqrt(1-i*i)+1)},easeInBack:function(e,t=1.70158){return e*e*((t+1)*e-t)},easeOutBack:function(e,t=1.70158){let i=e/1-1;return i*i*((t+1)*i+t)+1},easeInOutBack:function(e,t=1.70158){let i=2*e,n=i-2,s=1.525*t;return i<1?.5*i*i*((s+1)*i-s):.5*(n*n*((s+1)*n+s)+2)},easeInElastic:function(e,t=.7){if(0===e||1===e)return e;let i=e/1-1,n=1-t;return-(Math.pow(2,10*i)*Math.sin((i-n/(2*Math.PI)*Math.asin(1))*(2*Math.PI)/n))},easeOutElastic:function(e,t=.7){if(0===e||1===e)return e;let i=1-t,n=2*e;return Math.pow(2,-10*n)*Math.sin((n-i/(2*Math.PI)*Math.asin(1))*(2*Math.PI)/i)+1},easeInOutElastic:function(e,t=.65){if(0===e||1===e)return e;let i=1-t,n=2*e,s=n-1,a=i/(2*Math.PI)*Math.asin(1);return n<1?-.5*(Math.pow(2,10*s)*Math.sin((s-a)*(2*Math.PI)/i)):Math.pow(2,-10*s)*Math.sin((s-a)*(2*Math.PI)/i)*.5+1},easeInBounce:f,easeOutBounce:g,easeInOutBounce:function(e){return e<.5?.5*f(2*e):.5*g(2*e-1)+.5}};function g(e){let t=e/1;if(t<1/2.75)return 7.5625*t*t;if(t<2/2.75){let e=t-1.5/2.75;return 7.5625*e*e+.75}if(t<2.5/2.75){let e=t-2.25/2.75;return 7.5625*e*e+.9375}{let e=t-2.625/2.75;return 7.5625*e*e+.984375}}function f(e){return 1-g(1-e)}s=class Timeline{to(e,t,i,n){let s,a=0;if(void 0===n||isNaN(n)){if(this.animations.length>0){let e=this.animations[this.animations.length-1];e&&(a=e.duration+e.delay)}else a=0}else a=n;s=Array.isArray(e)?e:[e],this.animations.push({datas:s,duration:t,easing:i.easing||this.easing,onComplete:i.onComplete,onUpdate:i.onUpdate,values:[],delay:a,properties:i,isStarted:!1,isLast:!1,isFinished:!1});let r=0,o=0;for(let e of this.animations){let t=e.duration+e.delay;r<t&&(r=t,this.lastIndex=o),e.isLast=!1,o++}return this}start(){this.startTime=new Date,this.oldTime=new Date;let e=this.animations[this.lastIndex];e&&(e.isLast=!0),window.addEventListener("visibilitychange",this.onVisiblitychange),this.animate()}arrangeDatas(e){let{properties:t,datas:i,values:n}=e;for(let e in t){let s=0,a=[],r=[],o=[];switch(e){case"easing":case"onComplete":case"onUpdate":break;default:for(let n of i)null!==n&&"object"==typeof n&&(a[s]=n[e],r[s]=n[e],o[s]=t[e],s++);n.push({key:e,start:a,current:r,end:o})}}}calcProgress(e,t,i){return Math.max(0,Math.min(1,(i-e)/(t-e)))}calcLerp(e,t,i){return e+(t-e)*i}constructor(e){this.animate=()=>{let e=new Date;this.isWindowFocus||(this.oldTime=e);let t=e.getTime()-this.oldTime.getTime();for(let i of(this.time+=t,this.oldTime=e,this.animations)){let{datas:e,duration:t,easing:n,values:s,delay:a}=i;if(this.time>a&&!i.isFinished){i.isStarted||(i.isStarted=!0,this.arrangeDatas(i));let r=this.calcProgress(0,t,this.time-a),o=p[n];void 0!==o&&(r=o(r));for(let t=0;t<s.length;t++){let i=s[t];for(let t=0;t<e.length;t++){let n=e[t];void 0!==i&&(i.current[t]=this.calcLerp(i.start[t],i.end[t],r),"object"==typeof n&&null!==n&&(n[i.key]=i.current[t]))}}if(i.onUpdate){i.onUpdate();return}1===r&&(i.isFinished=!0,i.onComplete&&i.onComplete(),i.isLast&&(this.isFinished=!0))}}this.isFinished?(window.removeEventListener("visibilitychange",this.onVisiblitychange),this.onComplete()):(this.onUpdate(),requestAnimationFrame(this.animate))},this.onVisiblitychange=()=>{"visible"===document.visibilityState?this.isWindowFocus=!0:this.isWindowFocus=!1},this.easing=e.easing||"linear",this.options=e,this.onUpdate=e.onUpdate||function(){},this.onComplete=e.onComplete||function(){},this.isFinished=!1,this.lastIndex=0,this.isWindowFocus=!0,this.animations=[],this.startTime=new Date,this.oldTime=new Date,this.time=0}},a=class Animations{init(){setTimeout(()=>{this.createEntrance()},450)}createEntrance(){let e={radius:11,radian:2*Math.PI,lookat:new o.Pa4(0,0,0)},t=new s({onComplete:()=>{this.isFinishedEntrance=!0,this.createBlinkEyes()},easing:"linear",onUpdate:()=>{let t=Math.sin(e.radian)*e.radius,i=Math.cos(e.radian)*e.radius;u.camera.position.set(t,0,i),u.camera.lookAt(e.lookat)}});this.group.visible=!0,t.to(this.group.scale,1e3,{x:1,y:1,z:1,easing:"easeOutBack"},0).to(e,1500,{radian:0,easing:"easeOutBack"},0),t.start()}createBlinkEyes(){this.isBlinking=!0;let e=new s({onComplete:()=>{this.isBlinking=!1,this.blinkTimer=setTimeout(()=>{this.createBlinkEyes()},1e3*(2+4*Math.random()))},easing:"easeOutCubic"}),t=h.params.blinkingSpeed/1e3,i=this.eyesMeshes.left,n=this.eyesMeshes.right,a=[i?i.scale:1,n?n.scale:1];"blinkType1"==(.8>Math.random()?"blinkType1":"blinkType2")?e.to(a,.1/t,{y:.1},0).to(a,.3/t,{y:1},.2/t).start():e.to(a,.1/t,{y:.1},0).to(a,.2/t,{y:1},.2/t).to(a,.3/t,{y:.1}).to(a,.3/t,{y:1}).start()}createClickAnimation(){if(this.isFinishedEntrance&&!this.isPlayingClickAnim)switch("random"===h.params.clickAction?this.clickAction=h.getRandomAnim(this.isBlinking):this.clickAction=h.params.clickAction,this.clickAction){case"wink":if(this.isBlinking)return;this.blinkTimer&&clearTimeout(this.blinkTimer),this.createWink();break;case"jump1":case"jump2":case"jump3":this.createJump();break;case"shake":if(this.isBlinking)return;this.blinkTimer&&clearTimeout(this.blinkTimer),this.createShake()}}createWink(){if(this.isPlayingClickAnim)return;this.isPlayingClickAnim=!0;let e=new s({onComplete:()=>{this.isPlayingClickAnim=!1,this.blinkTimer=setTimeout(()=>{this.createBlinkEyes()},1e3*(1+3*Math.random()))},easing:"easeOutCubic"}),t=d.pos.current.x>0,i=t?this.eyesMeshes.left:this.eyesMeshes.right,n=i?i.scale:new o.Pa4(1,1,1),a=t?-.4:.4,r=[this.animationGroup.rotation],l=[this.animationGroup.scale],u=i?i.material.userData.uniforms:null;e.to(n,100,{y:.1},250).to(n,200,{y:1},650).to(u.uWink,100,{value:1},250).to(u.uWink,100,{value:0},650),e.to(l,200,{x:1.03,y:.97},0).to(l,400,{x:1,y:1},200).to(l,200,{x:1.02,y:.98},600).to(l,400,{x:1,y:1},800).to(r,250,{z:-(.2*a)},0).to(r,400,{z:a},250).to(r,600,{z:0},500).start()}createJump(){if(this.isPlayingClickAnim)return;this.isPlayingClickAnim=!0,"jump1"!==this.clickAction&&this.createRotating();let e=new s({onComplete:()=>{"jump1"===this.clickAction&&(this.isPlayingClickAnim=!1)},easing:"easeOutCubic"}),t=this.animationGroup.scale,i=this.animationGroup.position,n=this.animationGroup.rotation;e.to(t,200,{y:.95,x:1.05},0).to(t,100,{y:1.05,x:.95}).to(t,200,{y:1,x:1}).to(t,200,{y:1.03,x:.97}).to(t,200,{y:.97,x:1.03}).to(t,300,{y:1,x:1}).to(n,200,{x:.2},0).to(n,500,{x:-.1},200).to(n,300,{x:0},700).to(i,200,{y:-.2},0).to(i,300,{y:1.5},200).to(i,300,{y:-.2,easing:"easeInQuad"},500).to(i,200,{y:0},800);let a=[this.goggleGroup.position];switch(this.clickAction){case"jump2":case"jump3":e.to(a,400,{y:.6},300).to(a,400,{y:0},700).start();break;default:e.start()}}createRotating(){let e={anim_rotating:this.goggleGroup.userData.rotateX.anim_rotating},t=new s({onComplete:()=>{this.isPlayingClickAnim=!1},onUpdate:()=>{this.goggleGroup.userData.rotateX.anim_rotating=e.anim_rotating},easing:"easeOutCubic"}),i=0,n=[],a=[],r=0,o=100;for(let e=0;e<4;e++){let t=Math.random(),i=.1+.19999999999999998*t,s=150+50*t,l=.05+.05*t;3===e&&(l=0),e%2==0&&(i*=-1,l*=-1),n[e]={offset:i,duration:s,delay:r},a[e]={offset:l,duration:s,delay:o},r+=s,o+=s}let l=[this.animationGroup.rotation];for(let e of("jump3"===this.clickAction&&(i=2*Math.PI,t.to(l,200,{z:.1},0).to(l,200,{z:0},200).to(l,500,{y:Math.PI,easing:"easeInCubic"},0).to(l,700,{y:i,easing:"easeOutCubic"},500).to(l,400,{z:-.1},600).to(l,300,{z:0},1e3)),n))t.to(l,e.duration,{y:i+e.offset},e.delay+1300);for(let e of(t.to(l,300,{y:i,onComplete:()=>{this.animationGroup.rotation.y=0}}),a))t.to(l,e.duration,{z:e.offset,easing:"easeOutQuad"},e.delay+1300);t.to([e],700,{anim_rotating:.35},600).to([e],500,{anim_rotating:0},1500).start()}createShake(){if(this.isPlayingClickAnim)return;this.isPlayingClickAnim=!0;let e=new s({onComplete:()=>{this.isPlayingClickAnim=!1,this.blinkTimer=setTimeout(()=>{this.createBlinkEyes()},1e3*(1+3*Math.random()))},easing:"easeOutQuad"}),t=this.animationGroup.rotation,i=this.eyesMeshes.left,n=this.eyesMeshes.right,a=[i?i.scale:new o.Pa4(1,1,1),n?n.scale:new o.Pa4(1,1,1)],r=[i.material.userData.uniforms.uWink,n.material.userData.uniforms.uWink],l=[this.animationGroup.scale],u=[];for(let e=0;e<6;e++){let t=Math.random(),i=.1+.1*t,n=150+50*t;e%2==0&&(i*=-1),u[e]={offset:i,duration:n}}for(let i of u)e.to(t,i.duration,{y:i.offset});e.to(t,200,{y:0}).to(t,1e3,{x:-.3},0).to(t,500,{x:0},1e3).to(a,100,{y:.35},0).to(a,250,{y:1},1e3).to(r,100,{value:1.2},0).to(r,100,{value:0},1e3),e.to(l,200,{x:1.02,y:.98},0).to(l,400,{x:1,y:1},200).to(l,200,{x:1.02,y:.98},600).to(l,400,{x:1,y:1},800).start()}resize(){}update(){}constructor({group:e,animationGroup:t,goggleGroup:i,faceGroup:n,eyesMeshes:s}){this.group=e,this.goggleGroup=i,this.faceGroup=n,this.eyesMeshes=s,this.animationGroup=t,this.isFinishedEntrance=!1,this.isPlayingClickAnim=!1,this.isBlinking=!1,this.init()}};let y=`
#define MATCAP
varying vec3 vViewPosition;
uniform bool uIsEye;
uniform float uWink;
#include <common>
#include <uv_pars_vertex>
#include <color_pars_vertex>
#include <displacementmap_pars_vertex>
#include <fog_pars_vertex>
#include <normal_pars_vertex>
#include <morphtarget_pars_vertex>
#include <skinning_pars_vertex>
#include <logdepthbuf_pars_vertex>
#include <clipping_planes_pars_vertex>

float parabola(float x){
	return -pow(2.0 * x, 2.0);
}

void main() {
	#include <uv_vertex>
	#include <color_vertex>
	#include <morphcolor_vertex>
	#include <beginnormal_vertex>
	#include <morphnormal_vertex>
	#include <skinbase_vertex>
	#include <skinnormal_vertex>
	#include <defaultnormal_vertex>
	#include <normal_vertex>
	#include <begin_vertex>
	#include <morphtarget_vertex>
	#include <skinning_vertex>
	#include <displacementmap_vertex>

    vec4 worldPosition = modelMatrix * vec4(position, 1.0);
    vViewPosition.y = worldPosition.y;

	transformed.y += parabola(transformed.x) * uWink * 2.0;

	#include <project_vertex>
	#include <logdepthbuf_vertex>
	#include <clipping_planes_vertex>
	#include <fog_vertex>
	vViewPosition = - mvPosition.xyz;
}
`,v=`
#define MATCAP
uniform vec3 diffuse;
uniform float opacity;
uniform sampler2D matcap;
uniform float fresnelBias;
uniform float fresnelScale;
uniform float fresnelPower;
uniform float uFresnelIntensity;

varying vec3 vViewPosition;
#include <common>
#include <dithering_pars_fragment>
#include <color_pars_fragment>
#include <uv_pars_fragment>
#include <map_pars_fragment>
#include <alphamap_pars_fragment>
#include <alphatest_pars_fragment>
#include <fog_pars_fragment>
#include <normal_pars_fragment>
#include <bumpmap_pars_fragment>
#include <normalmap_pars_fragment>
#include <logdepthbuf_pars_fragment>
#include <clipping_planes_pars_fragment>
void main() {
	#include <clipping_planes_fragment>
	vec4 diffuseColor = vec4( diffuse, opacity );
	#include <logdepthbuf_fragment>
	#include <map_fragment>
	#include <color_fragment>
	#include <alphamap_fragment>
	#include <alphatest_fragment>
	#include <normal_fragment_begin>
	#include <normal_fragment_maps>
	vec3 viewDir = normalize( vViewPosition );
	vec3 x = normalize( vec3( viewDir.z, 0.0, - viewDir.x ) );
	vec3 y = cross( viewDir, x );
	vec2 uv = vec2( dot( x, normal ), dot( y, normal ) ) * 0.495 + 0.5;
	#ifdef USE_MATCAP
		vec4 matcapColor = texture2D( matcap, uv );
	#else
		vec4 matcapColor = vec4( vec3( mix( 0.2, 0.8, uv.y ) ), 1.0 );
	#endif
	vec3 outgoingLight = diffuseColor.rgb * matcapColor.rgb;
    float fresnel = fresnelBias + fresnelScale * pow(1.0 - dot(normalize(vNormal), vec3(0.0, 0.0, 1.0)), fresnelPower);
    float gradient = smoothstep(-0.0, 0.5, vNormal.y);
    float gradientPos = smoothstep(0.5, -1.0, vViewPosition.y); // Gradient based on y-position
    vec4 accentGreen = vec4(0.0, 0.4, 0.015, 1.0); // #00FF46
    vec4 fresnelColor = vec4(vec3(0.0, fresnel * gradient, fresnel * gradient * 0.275), fresnel*gradient);
    fresnelColor *= gradientPos;
	#include <output_fragment>
    gl_FragColor = mix(gl_FragColor, accentGreen, fresnelColor.a * uFresnelIntensity);

	#include <tonemapping_fragment>
	#include <encodings_fragment>
	#include <fog_fragment>
	#include <premultiplied_alpha_fragment>
	#include <dithering_fragment>
}
`;var k=i(1217);let _=class Assets{load(e){let t=[...this.loadImages(),...this.loadGltfs()];Promise.all(t).then(()=>{e&&e()})}loadGltfs(){let e=new k.E,t=Object.values(this.gltfs).map(t=>new Promise((i,n)=>{e.load(t.src,e=>{t.scene=e.scene,i(e.scene)},void 0,e=>n(e))}));return t}loadImages(){let e=new o.dpR,t=Object.values(this.images).map(t=>new Promise((i,n)=>{e.load(t.src,e=>{t.texture=e,e.flipY=t.flipY,t.encoding&&(e.encoding=t.encoding),i(e)},void 0,e=>n(e))}));return t}constructor(){let e="/images/modules/site/lab/";this.images={bakedGoggle:{src:`${e}bakedGoggle.png`,texture:null,flipY:!1,encoding:o.knz},bakedHead:{src:`${e}bakedHead.png`,texture:null,flipY:!1,encoding:o.knz},bakedOther:{src:`${e}bakedOther.png`,texture:null,flipY:!1,encoding:o.knz},matcap:{src:`${e}matcap.png`,texture:null,flipY:!0}},this.gltfs={head:{src:`${e}copilot.glb`,scene:null}}}},w=new _;r=class CopilotHead{init(){this.addMeshes(),this.createAnimations()}addMeshes(){let e=[];if(w.gltfs.head.scene)for(let t of(w.gltfs.head.scene.traverse(t=>{if(t.isMesh){let i;switch(t.name){case"Goggle":case"LeftEye":case"RightEye":case"Glass001":i=w.images.bakedGoggle.texture;break;case"HeadBase":i=w.images.bakedHead.texture;break;case"Screen":i=w.images.bakedOther.texture}let n="HeadBase"===t.name,s="RightEye"===t.name||"LeftEye"===t.name;switch(t.material=this.createMaterial(i,n,s),t.name){case"Goggle":case"Glass001":e.push({object:t,isGoggle:!0});break;default:e.push({object:t,isFace:!0})}"RightEye"===t.name?this.eyesMeshes.right=t:"LeftEye"===t.name&&(this.eyesMeshes.left=t)}}),e)){let e=t.object;e.parent&&e.parent.remove(e),t.isGoggle&&this.goggleGroup.add(e),t.isFace&&this.faceGroup.add(e)}}createMaterial(e,t,i){let n={uWink:{value:0}},s=new o.kaV({map:e,matcap:w.images.matcap.texture,transparent:!0});return s.onBeforeCompile=e=>{e.uniforms={...e.uniforms,...this.uniforms,uFresnelIntensity:{value:t?1:0},uIsEye:{value:i},...n},e.vertexShader=y,e.fragmentShader=v},s.userData.uniforms=n,s}createAnimations(){this.animations=new a({group:this.group,animationGroup:this.animationGroup,goggleGroup:this.goggleGroup,faceGroup:this.faceGroup,eyesMeshes:this.eyesMeshes}),d.addMousemoveFunc(this.raycast.bind(this)),document.body.addEventListener("click",()=>{this.isRaycastHit&&this.animations.createClickAnimation()})}raycast(){this.raycaster.setFromCamera(d.pos.target,u.camera);let e=this.raycaster.intersectObject(this.group);e.length>0?this.isRaycastHit=!0:this.isRaycastHit=!1}update(){var e,t;this.breathing.rotation=.05*Math.sin(2*u.time),this.breathing.position=.08*Math.sin((u.time+.25)*2);let i=d.pos.target.length();this.mouseIntensity.target=n(3,1.3,i),this.mouseIntensity.current+=(this.mouseIntensity.target-this.mouseIntensity.current)*u.getEase(3),this.lookatTarget.set(.5*d.pos.current.x,.5*d.pos.current.y,1),this.lookatTarget.lerp(this.lookatTarget_default,1-this.mouseIntensity.current),this.lookatTarget.y+=this.breathing.rotation,this.orientationGroup.lookAt(this.lookatTarget),this.orientationGroup.position.y=this.breathing.position;let s=n(0,.5,d.pos.current2.y);for(let i in s=Math.min(s=(e=0)+(s-e)*this.mouseIntensity.current+(this.breathing.rotation+1)*1.2,1),this.goggleGroup.userData.rotateX.orientation=(t=.1)+(-.08-t)*s,this.goggleGroup.rotation.x=0,this.goggleGroup.userData.rotateX){let e=this.goggleGroup.userData.rotateX[i];this.goggleGroup.rotation.x+=e}}constructor(){this.group=new o.ZAu,this.group.visible=!1,this.group.scale.set(.01,.01,.01),this.group.position.y,this.orientationGroup=new o.ZAu,this.animationGroup=new o.ZAu,this.group.add(this.orientationGroup),this.orientationGroup.add(this.animationGroup),this.goggleGroup=new o.ZAu,this.faceGroup=new o.ZAu,this.animationGroup.add(this.goggleGroup),this.animationGroup.add(this.faceGroup),this.eyesMeshes={left:null,right:null},this.breathing={rotation:0,position:0},this.goggleGroup.userData.rotateX={orientation:0,anim_rotating:0},this.lookatTarget=new o.Pa4,this.lookatTarget_default=new o.Pa4(-.4,-.2,1),this.raycaster=new o.iMs,this.isRaycastHit=!1,this.mouseIntensity={target:0,current:0},this.uniforms={fresnelBias:{value:.01},fresnelScale:{value:2},fresnelPower:{value:3}}}};var x=i(36071);let M={width:500,height:500},b=!1;(0,x.N7)(".js-copilot-head",e=>{let t=e.getBoundingClientRect();M.width=t.width,M.height=t.height;let i=document.querySelector(".js-copilot-head-wrapper");u.init(i,e),h.init(),d.init();let n=new r;b||w.load(()=>{n.init(),b=!0}),u.scene.add(n.group),window.addEventListener("resize",()=>{u.resize()}),window.addEventListener("scroll",()=>{u.resize()});let s=()=>{u.update(),d.update(),n.update(),u.renderer.render(u.scene,u.camera),window.requestAnimationFrame(s)};s()})}},e=>{var t=t=>e(e.s=t);e.O(0,["vendors-node_modules_github_selector-observer_dist_index_esm_js","vendors-node_modules_three_build_three_module_js","vendors-node_modules_three_examples_jsm_loaders_GLTFLoader_js"],()=>t(70245)),e.O()}]);
//# sourceMappingURL=marketing-copilot-head-430a58f717b5.js.map