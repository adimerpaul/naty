import{g as xn}from"./_commonjsHelpers-CqkleIqs.js";import{v as se}from"./index-Bc7zoEtR.js";var $e={},Pt,Ur;function Si(){return Ur||(Ur=1,Pt=function(){return typeof Promise=="function"&&Promise.prototype&&Promise.prototype.then}),Pt}var $t={},Te={},zr;function Ae(){if(zr)return Te;zr=1;let e;const t=[0,26,44,70,100,134,172,196,242,292,346,404,466,532,581,655,733,815,901,991,1085,1156,1258,1364,1474,1588,1706,1828,1921,2051,2185,2323,2465,2611,2761,2876,3034,3196,3362,3532,3706];return Te.getSymbolSize=function(n){if(!n)throw new Error('"version" cannot be null or undefined');if(n<1||n>40)throw new Error('"version" should be in range from 1 to 40');return n*4+17},Te.getSymbolTotalCodewords=function(n){return t[n]},Te.getBCHDigit=function(r){let n=0;for(;r!==0;)n++,r>>>=1;return n},Te.setToSJISFunction=function(n){if(typeof n!="function")throw new Error('"toSJISFunc" is not a valid function.');e=n},Te.isKanjiModeEnabled=function(){return typeof e<"u"},Te.toSJIS=function(n){return e(n)},Te}var Lt={},Hr;function pr(){return Hr||(Hr=1,(function(e){e.L={bit:1},e.M={bit:0},e.Q={bit:3},e.H={bit:2};function t(r){if(typeof r!="string")throw new Error("Param is not a string");switch(r.toLowerCase()){case"l":case"low":return e.L;case"m":case"medium":return e.M;case"q":case"quartile":return e.Q;case"h":case"high":return e.H;default:throw new Error("Unknown EC Level: "+r)}}e.isValid=function(n){return n&&typeof n.bit<"u"&&n.bit>=0&&n.bit<4},e.from=function(n,i){if(e.isValid(n))return n;try{return t(n)}catch{return i}}})(Lt)),Lt}var It,Wr;function Mi(){if(Wr)return It;Wr=1;function e(){this.buffer=[],this.length=0}return e.prototype={get:function(t){const r=Math.floor(t/8);return(this.buffer[r]>>>7-t%8&1)===1},put:function(t,r){for(let n=0;n<r;n++)this.putBit((t>>>r-n-1&1)===1)},getLengthInBits:function(){return this.length},putBit:function(t){const r=Math.floor(this.length/8);this.buffer.length<=r&&this.buffer.push(0),t&&(this.buffer[r]|=128>>>this.length%8),this.length++}},It=e,It}var Yt,Vr;function Ti(){if(Vr)return Yt;Vr=1;function e(t){if(!t||t<1)throw new Error("BitMatrix size must be defined and greater than 0");this.size=t,this.data=new Uint8Array(t*t),this.reservedBit=new Uint8Array(t*t)}return e.prototype.set=function(t,r,n,i){const s=t*this.size+r;this.data[s]=n,i&&(this.reservedBit[s]=!0)},e.prototype.get=function(t,r){return this.data[t*this.size+r]},e.prototype.xor=function(t,r,n){this.data[t*this.size+r]^=n},e.prototype.isReserved=function(t,r){return this.reservedBit[t*this.size+r]},Yt=e,Yt}var Bt={},jr;function xi(){return jr||(jr=1,(function(e){const t=Ae().getSymbolSize;e.getRowColCoords=function(n){if(n===1)return[];const i=Math.floor(n/7)+2,s=t(n),a=s===145?26:Math.ceil((s-13)/(2*i-2))*2,l=[s-7];for(let o=1;o<i-1;o++)l[o]=l[o-1]-a;return l.push(6),l.reverse()},e.getPositions=function(n){const i=[],s=e.getRowColCoords(n),a=s.length;for(let l=0;l<a;l++)for(let o=0;o<a;o++)l===0&&o===0||l===0&&o===a-1||l===a-1&&o===0||i.push([s[l],s[o]]);return i}})(Bt)),Bt}var Ut={},qr;function Di(){if(qr)return Ut;qr=1;const e=Ae().getSymbolSize,t=7;return Ut.getPositions=function(n){const i=e(n);return[[0,0],[i-t,0],[0,i-t]]},Ut}var zt={},Gr;function Ci(){return Gr||(Gr=1,(function(e){e.Patterns={PATTERN000:0,PATTERN001:1,PATTERN010:2,PATTERN011:3,PATTERN100:4,PATTERN101:5,PATTERN110:6,PATTERN111:7};const t={N1:3,N2:3,N3:40,N4:10};e.isValid=function(i){return i!=null&&i!==""&&!isNaN(i)&&i>=0&&i<=7},e.from=function(i){return e.isValid(i)?parseInt(i,10):void 0},e.getPenaltyN1=function(i){const s=i.size;let a=0,l=0,o=0,d=null,c=null;for(let u=0;u<s;u++){l=o=0,d=c=null;for(let h=0;h<s;h++){let f=i.get(u,h);f===d?l++:(l>=5&&(a+=t.N1+(l-5)),d=f,l=1),f=i.get(h,u),f===c?o++:(o>=5&&(a+=t.N1+(o-5)),c=f,o=1)}l>=5&&(a+=t.N1+(l-5)),o>=5&&(a+=t.N1+(o-5))}return a},e.getPenaltyN2=function(i){const s=i.size;let a=0;for(let l=0;l<s-1;l++)for(let o=0;o<s-1;o++){const d=i.get(l,o)+i.get(l,o+1)+i.get(l+1,o)+i.get(l+1,o+1);(d===4||d===0)&&a++}return a*t.N2},e.getPenaltyN3=function(i){const s=i.size;let a=0,l=0,o=0;for(let d=0;d<s;d++){l=o=0;for(let c=0;c<s;c++)l=l<<1&2047|i.get(d,c),c>=10&&(l===1488||l===93)&&a++,o=o<<1&2047|i.get(c,d),c>=10&&(o===1488||o===93)&&a++}return a*t.N3},e.getPenaltyN4=function(i){let s=0;const a=i.data.length;for(let o=0;o<a;o++)s+=i.data[o];return Math.abs(Math.ceil(s*100/a/5)-10)*t.N4};function r(n,i,s){switch(n){case e.Patterns.PATTERN000:return(i+s)%2===0;case e.Patterns.PATTERN001:return i%2===0;case e.Patterns.PATTERN010:return s%3===0;case e.Patterns.PATTERN011:return(i+s)%3===0;case e.Patterns.PATTERN100:return(Math.floor(i/2)+Math.floor(s/3))%2===0;case e.Patterns.PATTERN101:return i*s%2+i*s%3===0;case e.Patterns.PATTERN110:return(i*s%2+i*s%3)%2===0;case e.Patterns.PATTERN111:return(i*s%3+(i+s)%2)%2===0;default:throw new Error("bad maskPattern:"+n)}}e.applyMask=function(i,s){const a=s.size;for(let l=0;l<a;l++)for(let o=0;o<a;o++)s.isReserved(o,l)||s.xor(o,l,r(i,o,l))},e.getBestMask=function(i,s){const a=Object.keys(e.Patterns).length;let l=0,o=1/0;for(let d=0;d<a;d++){s(d),e.applyMask(d,i);const c=e.getPenaltyN1(i)+e.getPenaltyN2(i)+e.getPenaltyN3(i)+e.getPenaltyN4(i);e.applyMask(d,i),c<o&&(o=c,l=d)}return l}})(zt)),zt}var dt={},Zr;function Dn(){if(Zr)return dt;Zr=1;const e=pr(),t=[1,1,1,1,1,1,1,1,1,1,2,2,1,2,2,4,1,2,4,4,2,4,4,4,2,4,6,5,2,4,6,6,2,5,8,8,4,5,8,8,4,5,8,11,4,8,10,11,4,9,12,16,4,9,16,16,6,10,12,18,6,10,17,16,6,11,16,19,6,13,18,21,7,14,21,25,8,16,20,25,8,17,23,25,9,17,23,34,9,18,25,30,10,20,27,32,12,21,29,35,12,23,34,37,12,25,34,40,13,26,35,42,14,28,38,45,15,29,40,48,16,31,43,51,17,33,45,54,18,35,48,57,19,37,51,60,19,38,53,63,20,40,56,66,21,43,59,70,22,45,62,74,24,47,65,77,25,49,68,81],r=[7,10,13,17,10,16,22,28,15,26,36,44,20,36,52,64,26,48,72,88,36,64,96,112,40,72,108,130,48,88,132,156,60,110,160,192,72,130,192,224,80,150,224,264,96,176,260,308,104,198,288,352,120,216,320,384,132,240,360,432,144,280,408,480,168,308,448,532,180,338,504,588,196,364,546,650,224,416,600,700,224,442,644,750,252,476,690,816,270,504,750,900,300,560,810,960,312,588,870,1050,336,644,952,1110,360,700,1020,1200,390,728,1050,1260,420,784,1140,1350,450,812,1200,1440,480,868,1290,1530,510,924,1350,1620,540,980,1440,1710,570,1036,1530,1800,570,1064,1590,1890,600,1120,1680,1980,630,1204,1770,2100,660,1260,1860,2220,720,1316,1950,2310,750,1372,2040,2430];return dt.getBlocksCount=function(i,s){switch(s){case e.L:return t[(i-1)*4+0];case e.M:return t[(i-1)*4+1];case e.Q:return t[(i-1)*4+2];case e.H:return t[(i-1)*4+3];default:return}},dt.getTotalCodewordsCount=function(i,s){switch(s){case e.L:return r[(i-1)*4+0];case e.M:return r[(i-1)*4+1];case e.Q:return r[(i-1)*4+2];case e.H:return r[(i-1)*4+3];default:return}},dt}var Ht={},Ge={},Jr;function Ei(){if(Jr)return Ge;Jr=1;const e=new Uint8Array(512),t=new Uint8Array(256);return(function(){let n=1;for(let i=0;i<255;i++)e[i]=n,t[n]=i,n<<=1,n&256&&(n^=285);for(let i=255;i<512;i++)e[i]=e[i-255]})(),Ge.log=function(n){if(n<1)throw new Error("log("+n+")");return t[n]},Ge.exp=function(n){return e[n]},Ge.mul=function(n,i){return n===0||i===0?0:e[t[n]+t[i]]},Ge}var Kr;function ki(){return Kr||(Kr=1,(function(e){const t=Ei();e.mul=function(n,i){const s=new Uint8Array(n.length+i.length-1);for(let a=0;a<n.length;a++)for(let l=0;l<i.length;l++)s[a+l]^=t.mul(n[a],i[l]);return s},e.mod=function(n,i){let s=new Uint8Array(n);for(;s.length-i.length>=0;){const a=s[0];for(let o=0;o<i.length;o++)s[o]^=t.mul(i[o],a);let l=0;for(;l<s.length&&s[l]===0;)l++;s=s.slice(l)}return s},e.generateECPolynomial=function(n){let i=new Uint8Array([1]);for(let s=0;s<n;s++)i=e.mul(i,new Uint8Array([1,t.exp(s)]));return i}})(Ht)),Ht}var Wt,Qr;function Ni(){if(Qr)return Wt;Qr=1;const e=ki();function t(r){this.genPoly=void 0,this.degree=r,this.degree&&this.initialize(this.degree)}return t.prototype.initialize=function(n){this.degree=n,this.genPoly=e.generateECPolynomial(this.degree)},t.prototype.encode=function(n){if(!this.genPoly)throw new Error("Encoder not initialized");const i=new Uint8Array(n.length+this.degree);i.set(n);const s=e.mod(i,this.genPoly),a=this.degree-s.length;if(a>0){const l=new Uint8Array(this.degree);return l.set(s,a),l}return s},Wt=t,Wt}var Vt={},jt={},qt={},Xr;function Cn(){return Xr||(Xr=1,qt.isValid=function(t){return!isNaN(t)&&t>=1&&t<=40}),qt}var ae={},en;function En(){if(en)return ae;en=1;const e="[0-9]+",t="[A-Z $%*+\\-./:]+";let r="(?:[u3000-u303F]|[u3040-u309F]|[u30A0-u30FF]|[uFF00-uFFEF]|[u4E00-u9FAF]|[u2605-u2606]|[u2190-u2195]|u203B|[u2010u2015u2018u2019u2025u2026u201Cu201Du2225u2260]|[u0391-u0451]|[u00A7u00A8u00B1u00B4u00D7u00F7])+";r=r.replace(/u/g,"\\u");const n="(?:(?![A-Z0-9 $%*+\\-./:]|"+r+`)(?:.|[\r
]))+`;ae.KANJI=new RegExp(r,"g"),ae.BYTE_KANJI=new RegExp("[^A-Z0-9 $%*+\\-./:]+","g"),ae.BYTE=new RegExp(n,"g"),ae.NUMERIC=new RegExp(e,"g"),ae.ALPHANUMERIC=new RegExp(t,"g");const i=new RegExp("^"+r+"$"),s=new RegExp("^"+e+"$"),a=new RegExp("^[A-Z0-9 $%*+\\-./:]+$");return ae.testKanji=function(o){return i.test(o)},ae.testNumeric=function(o){return s.test(o)},ae.testAlphanumeric=function(o){return a.test(o)},ae}var tn;function Fe(){return tn||(tn=1,(function(e){const t=Cn(),r=En();e.NUMERIC={id:"Numeric",bit:1,ccBits:[10,12,14]},e.ALPHANUMERIC={id:"Alphanumeric",bit:2,ccBits:[9,11,13]},e.BYTE={id:"Byte",bit:4,ccBits:[8,16,16]},e.KANJI={id:"Kanji",bit:8,ccBits:[8,10,12]},e.MIXED={bit:-1},e.getCharCountIndicator=function(s,a){if(!s.ccBits)throw new Error("Invalid mode: "+s);if(!t.isValid(a))throw new Error("Invalid version: "+a);return a>=1&&a<10?s.ccBits[0]:a<27?s.ccBits[1]:s.ccBits[2]},e.getBestModeForData=function(s){return r.testNumeric(s)?e.NUMERIC:r.testAlphanumeric(s)?e.ALPHANUMERIC:r.testKanji(s)?e.KANJI:e.BYTE},e.toString=function(s){if(s&&s.id)return s.id;throw new Error("Invalid mode")},e.isValid=function(s){return s&&s.bit&&s.ccBits};function n(i){if(typeof i!="string")throw new Error("Param is not a string");switch(i.toLowerCase()){case"numeric":return e.NUMERIC;case"alphanumeric":return e.ALPHANUMERIC;case"kanji":return e.KANJI;case"byte":return e.BYTE;default:throw new Error("Unknown mode: "+i)}}e.from=function(s,a){if(e.isValid(s))return s;try{return n(s)}catch{return a}}})(jt)),jt}var rn;function Oi(){return rn||(rn=1,(function(e){const t=Ae(),r=Dn(),n=pr(),i=Fe(),s=Cn(),a=7973,l=t.getBCHDigit(a);function o(h,f,g){for(let _=1;_<=40;_++)if(f<=e.getCapacity(_,g,h))return _}function d(h,f){return i.getCharCountIndicator(h,f)+4}function c(h,f){let g=0;return h.forEach(function(_){const M=d(_.mode,f);g+=M+_.getBitsLength()}),g}function u(h,f){for(let g=1;g<=40;g++)if(c(h,g)<=e.getCapacity(g,f,i.MIXED))return g}e.from=function(f,g){return s.isValid(f)?parseInt(f,10):g},e.getCapacity=function(f,g,_){if(!s.isValid(f))throw new Error("Invalid QR Code version");typeof _>"u"&&(_=i.BYTE);const M=t.getSymbolTotalCodewords(f),p=r.getTotalCodewordsCount(f,g),D=(M-p)*8;if(_===i.MIXED)return D;const E=D-d(_,f);switch(_){case i.NUMERIC:return Math.floor(E/10*3);case i.ALPHANUMERIC:return Math.floor(E/11*2);case i.KANJI:return Math.floor(E/13);case i.BYTE:default:return Math.floor(E/8)}},e.getBestVersionForData=function(f,g){let _;const M=n.from(g,n.M);if(Array.isArray(f)){if(f.length>1)return u(f,M);if(f.length===0)return 1;_=f[0]}else _=f;return o(_.mode,_.getLength(),M)},e.getEncodedBits=function(f){if(!s.isValid(f)||f<7)throw new Error("Invalid QR Code version");let g=f<<12;for(;t.getBCHDigit(g)-l>=0;)g^=a<<t.getBCHDigit(g)-l;return f<<12|g}})(Vt)),Vt}var Gt={},nn;function Ai(){if(nn)return Gt;nn=1;const e=Ae(),t=1335,r=21522,n=e.getBCHDigit(t);return Gt.getEncodedBits=function(s,a){const l=s.bit<<3|a;let o=l<<10;for(;e.getBCHDigit(o)-n>=0;)o^=t<<e.getBCHDigit(o)-n;return(l<<10|o)^r},Gt}var Zt={},Jt,sn;function Fi(){if(sn)return Jt;sn=1;const e=Fe();function t(r){this.mode=e.NUMERIC,this.data=r.toString()}return t.getBitsLength=function(n){return 10*Math.floor(n/3)+(n%3?n%3*3+1:0)},t.prototype.getLength=function(){return this.data.length},t.prototype.getBitsLength=function(){return t.getBitsLength(this.data.length)},t.prototype.write=function(n){let i,s,a;for(i=0;i+3<=this.data.length;i+=3)s=this.data.substr(i,3),a=parseInt(s,10),n.put(a,10);const l=this.data.length-i;l>0&&(s=this.data.substr(i),a=parseInt(s,10),n.put(a,l*3+1))},Jt=t,Jt}var Kt,an;function Ri(){if(an)return Kt;an=1;const e=Fe(),t=["0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"," ","$","%","*","+","-",".","/",":"];function r(n){this.mode=e.ALPHANUMERIC,this.data=n}return r.getBitsLength=function(i){return 11*Math.floor(i/2)+6*(i%2)},r.prototype.getLength=function(){return this.data.length},r.prototype.getBitsLength=function(){return r.getBitsLength(this.data.length)},r.prototype.write=function(i){let s;for(s=0;s+2<=this.data.length;s+=2){let a=t.indexOf(this.data[s])*45;a+=t.indexOf(this.data[s+1]),i.put(a,11)}this.data.length%2&&i.put(t.indexOf(this.data[s]),6)},Kt=r,Kt}var Qt,on;function Pi(){if(on)return Qt;on=1;const e=Fe();function t(r){this.mode=e.BYTE,typeof r=="string"?this.data=new TextEncoder().encode(r):this.data=new Uint8Array(r)}return t.getBitsLength=function(n){return n*8},t.prototype.getLength=function(){return this.data.length},t.prototype.getBitsLength=function(){return t.getBitsLength(this.data.length)},t.prototype.write=function(r){for(let n=0,i=this.data.length;n<i;n++)r.put(this.data[n],8)},Qt=t,Qt}var Xt,ln;function $i(){if(ln)return Xt;ln=1;const e=Fe(),t=Ae();function r(n){this.mode=e.KANJI,this.data=n}return r.getBitsLength=function(i){return i*13},r.prototype.getLength=function(){return this.data.length},r.prototype.getBitsLength=function(){return r.getBitsLength(this.data.length)},r.prototype.write=function(n){let i;for(i=0;i<this.data.length;i++){let s=t.toSJIS(this.data[i]);if(s>=33088&&s<=40956)s-=33088;else if(s>=57408&&s<=60351)s-=49472;else throw new Error("Invalid SJIS character: "+this.data[i]+`
Make sure your charset is UTF-8`);s=(s>>>8&255)*192+(s&255),n.put(s,13)}},Xt=r,Xt}var er={exports:{}},dn;function Li(){return dn||(dn=1,(function(e){var t={single_source_shortest_paths:function(r,n,i){var s={},a={};a[n]=0;var l=t.PriorityQueue.make();l.push(n,0);for(var o,d,c,u,h,f,g,_,M;!l.empty();){o=l.pop(),d=o.value,u=o.cost,h=r[d]||{};for(c in h)h.hasOwnProperty(c)&&(f=h[c],g=u+f,_=a[c],M=typeof a[c]>"u",(M||_>g)&&(a[c]=g,l.push(c,g),s[c]=d))}if(typeof i<"u"&&typeof a[i]>"u"){var p=["Could not find a path from ",n," to ",i,"."].join("");throw new Error(p)}return s},extract_shortest_path_from_predecessor_list:function(r,n){for(var i=[],s=n;s;)i.push(s),r[s],s=r[s];return i.reverse(),i},find_path:function(r,n,i){var s=t.single_source_shortest_paths(r,n,i);return t.extract_shortest_path_from_predecessor_list(s,i)},PriorityQueue:{make:function(r){var n=t.PriorityQueue,i={},s;r=r||{};for(s in n)n.hasOwnProperty(s)&&(i[s]=n[s]);return i.queue=[],i.sorter=r.sorter||n.default_sorter,i},default_sorter:function(r,n){return r.cost-n.cost},push:function(r,n){var i={value:r,cost:n};this.queue.push(i),this.queue.sort(this.sorter)},pop:function(){return this.queue.shift()},empty:function(){return this.queue.length===0}}};e.exports=t})(er)),er.exports}var cn;function Ii(){return cn||(cn=1,(function(e){const t=Fe(),r=Fi(),n=Ri(),i=Pi(),s=$i(),a=En(),l=Ae(),o=Li();function d(p){return unescape(encodeURIComponent(p)).length}function c(p,D,E){const w=[];let B;for(;(B=p.exec(E))!==null;)w.push({data:B[0],index:B.index,mode:D,length:B[0].length});return w}function u(p){const D=c(a.NUMERIC,t.NUMERIC,p),E=c(a.ALPHANUMERIC,t.ALPHANUMERIC,p);let w,B;return l.isKanjiModeEnabled()?(w=c(a.BYTE,t.BYTE,p),B=c(a.KANJI,t.KANJI,p)):(w=c(a.BYTE_KANJI,t.BYTE,p),B=[]),D.concat(E,w,B).sort(function(N,k){return N.index-k.index}).map(function(N){return{data:N.data,mode:N.mode,length:N.length}})}function h(p,D){switch(D){case t.NUMERIC:return r.getBitsLength(p);case t.ALPHANUMERIC:return n.getBitsLength(p);case t.KANJI:return s.getBitsLength(p);case t.BYTE:return i.getBitsLength(p)}}function f(p){return p.reduce(function(D,E){const w=D.length-1>=0?D[D.length-1]:null;return w&&w.mode===E.mode?(D[D.length-1].data+=E.data,D):(D.push(E),D)},[])}function g(p){const D=[];for(let E=0;E<p.length;E++){const w=p[E];switch(w.mode){case t.NUMERIC:D.push([w,{data:w.data,mode:t.ALPHANUMERIC,length:w.length},{data:w.data,mode:t.BYTE,length:w.length}]);break;case t.ALPHANUMERIC:D.push([w,{data:w.data,mode:t.BYTE,length:w.length}]);break;case t.KANJI:D.push([w,{data:w.data,mode:t.BYTE,length:d(w.data)}]);break;case t.BYTE:D.push([{data:w.data,mode:t.BYTE,length:d(w.data)}])}}return D}function _(p,D){const E={},w={start:{}};let B=["start"];for(let T=0;T<p.length;T++){const N=p[T],k=[];for(let S=0;S<N.length;S++){const O=N[S],C=""+T+S;k.push(C),E[C]={node:O,lastCount:0},w[C]={};for(let A=0;A<B.length;A++){const v=B[A];E[v]&&E[v].node.mode===O.mode?(w[v][C]=h(E[v].lastCount+O.length,O.mode)-h(E[v].lastCount,O.mode),E[v].lastCount+=O.length):(E[v]&&(E[v].lastCount=O.length),w[v][C]=h(O.length,O.mode)+4+t.getCharCountIndicator(O.mode,D))}}B=k}for(let T=0;T<B.length;T++)w[B[T]].end=0;return{map:w,table:E}}function M(p,D){let E;const w=t.getBestModeForData(p);if(E=t.from(D,w),E!==t.BYTE&&E.bit<w.bit)throw new Error('"'+p+'" cannot be encoded with mode '+t.toString(E)+`.
 Suggested mode is: `+t.toString(w));switch(E===t.KANJI&&!l.isKanjiModeEnabled()&&(E=t.BYTE),E){case t.NUMERIC:return new r(p);case t.ALPHANUMERIC:return new n(p);case t.KANJI:return new s(p);case t.BYTE:return new i(p)}}e.fromArray=function(D){return D.reduce(function(E,w){return typeof w=="string"?E.push(M(w,null)):w.data&&E.push(M(w.data,w.mode)),E},[])},e.fromString=function(D,E){const w=u(D,l.isKanjiModeEnabled()),B=g(w),T=_(B,E),N=o.find_path(T.map,"start","end"),k=[];for(let S=1;S<N.length-1;S++)k.push(T.table[N[S]].node);return e.fromArray(f(k))},e.rawSplit=function(D){return e.fromArray(u(D,l.isKanjiModeEnabled()))}})(Zt)),Zt}var un;function Yi(){if(un)return $t;un=1;const e=Ae(),t=pr(),r=Mi(),n=Ti(),i=xi(),s=Di(),a=Ci(),l=Dn(),o=Ni(),d=Oi(),c=Ai(),u=Fe(),h=Ii();function f(T,N){const k=T.size,S=s.getPositions(N);for(let O=0;O<S.length;O++){const C=S[O][0],A=S[O][1];for(let v=-1;v<=7;v++)if(!(C+v<=-1||k<=C+v))for(let F=-1;F<=7;F++)A+F<=-1||k<=A+F||(v>=0&&v<=6&&(F===0||F===6)||F>=0&&F<=6&&(v===0||v===6)||v>=2&&v<=4&&F>=2&&F<=4?T.set(C+v,A+F,!0,!0):T.set(C+v,A+F,!1,!0))}}function g(T){const N=T.size;for(let k=8;k<N-8;k++){const S=k%2===0;T.set(k,6,S,!0),T.set(6,k,S,!0)}}function _(T,N){const k=i.getPositions(N);for(let S=0;S<k.length;S++){const O=k[S][0],C=k[S][1];for(let A=-2;A<=2;A++)for(let v=-2;v<=2;v++)A===-2||A===2||v===-2||v===2||A===0&&v===0?T.set(O+A,C+v,!0,!0):T.set(O+A,C+v,!1,!0)}}function M(T,N){const k=T.size,S=d.getEncodedBits(N);let O,C,A;for(let v=0;v<18;v++)O=Math.floor(v/3),C=v%3+k-8-3,A=(S>>v&1)===1,T.set(O,C,A,!0),T.set(C,O,A,!0)}function p(T,N,k){const S=T.size,O=c.getEncodedBits(N,k);let C,A;for(C=0;C<15;C++)A=(O>>C&1)===1,C<6?T.set(C,8,A,!0):C<8?T.set(C+1,8,A,!0):T.set(S-15+C,8,A,!0),C<8?T.set(8,S-C-1,A,!0):C<9?T.set(8,15-C-1+1,A,!0):T.set(8,15-C-1,A,!0);T.set(S-8,8,1,!0)}function D(T,N){const k=T.size;let S=-1,O=k-1,C=7,A=0;for(let v=k-1;v>0;v-=2)for(v===6&&v--;;){for(let F=0;F<2;F++)if(!T.isReserved(O,v-F)){let ie=!1;A<N.length&&(ie=(N[A]>>>C&1)===1),T.set(O,v-F,ie),C--,C===-1&&(A++,C=7)}if(O+=S,O<0||k<=O){O-=S,S=-S;break}}}function E(T,N,k){const S=new r;k.forEach(function(F){S.put(F.mode.bit,4),S.put(F.getLength(),u.getCharCountIndicator(F.mode,T)),F.write(S)});const O=e.getSymbolTotalCodewords(T),C=l.getTotalCodewordsCount(T,N),A=(O-C)*8;for(S.getLengthInBits()+4<=A&&S.put(0,4);S.getLengthInBits()%8!==0;)S.putBit(0);const v=(A-S.getLengthInBits())/8;for(let F=0;F<v;F++)S.put(F%2?17:236,8);return w(S,T,N)}function w(T,N,k){const S=e.getSymbolTotalCodewords(N),O=l.getTotalCodewordsCount(N,k),C=S-O,A=l.getBlocksCount(N,k),v=S%A,F=A-v,ie=Math.floor(S/A),Ee=Math.floor(C/A),at=Ee+1,je=ie-Ee,ot=new o(je);let qe=0;const lt=new Array(A),Yr=new Array(A);let At=0;const _i=new Uint8Array(T.buffer);for(let Pe=0;Pe<A;Pe++){const Rt=Pe<F?Ee:at;lt[Pe]=_i.slice(qe,qe+Rt),Yr[Pe]=ot.encode(lt[Pe]),qe+=Rt,At=Math.max(At,Rt)}const Ft=new Uint8Array(S);let Br=0,ue,he;for(ue=0;ue<At;ue++)for(he=0;he<A;he++)ue<lt[he].length&&(Ft[Br++]=lt[he][ue]);for(ue=0;ue<je;ue++)for(he=0;he<A;he++)Ft[Br++]=Yr[he][ue];return Ft}function B(T,N,k,S){let O;if(Array.isArray(T))O=h.fromArray(T);else if(typeof T=="string"){let ie=N;if(!ie){const Ee=h.rawSplit(T);ie=d.getBestVersionForData(Ee,k)}O=h.fromString(T,ie||40)}else throw new Error("Invalid data");const C=d.getBestVersionForData(O,k);if(!C)throw new Error("The amount of data is too big to be stored in a QR Code");if(!N)N=C;else if(N<C)throw new Error(`
The chosen QR Code version cannot contain this amount of data.
Minimum version required to store current data is: `+C+`.
`);const A=E(N,k,O),v=e.getSymbolSize(N),F=new n(v);return f(F,N),g(F),_(F,N),p(F,k,0),N>=7&&M(F,N),D(F,A),isNaN(S)&&(S=a.getBestMask(F,p.bind(null,F,k))),a.applyMask(S,F),p(F,k,S),{modules:F,version:N,errorCorrectionLevel:k,maskPattern:S,segments:O}}return $t.create=function(N,k){if(typeof N>"u"||N==="")throw new Error("No input text");let S=t.M,O,C;return typeof k<"u"&&(S=t.from(k.errorCorrectionLevel,t.M),O=d.from(k.version),C=a.from(k.maskPattern),k.toSJISFunc&&e.setToSJISFunction(k.toSJISFunc)),B(N,O,S,C)},$t}var tr={},rr={},hn;function kn(){return hn||(hn=1,(function(e){function t(r){if(typeof r=="number"&&(r=r.toString()),typeof r!="string")throw new Error("Color should be defined as hex string");let n=r.slice().replace("#","").split("");if(n.length<3||n.length===5||n.length>8)throw new Error("Invalid hex color: "+r);(n.length===3||n.length===4)&&(n=Array.prototype.concat.apply([],n.map(function(s){return[s,s]}))),n.length===6&&n.push("F","F");const i=parseInt(n.join(""),16);return{r:i>>24&255,g:i>>16&255,b:i>>8&255,a:i&255,hex:"#"+n.slice(0,6).join("")}}e.getOptions=function(n){n||(n={}),n.color||(n.color={});const i=typeof n.margin>"u"||n.margin===null||n.margin<0?4:n.margin,s=n.width&&n.width>=21?n.width:void 0,a=n.scale||4;return{width:s,scale:s?4:a,margin:i,color:{dark:t(n.color.dark||"#000000ff"),light:t(n.color.light||"#ffffffff")},type:n.type,rendererOpts:n.rendererOpts||{}}},e.getScale=function(n,i){return i.width&&i.width>=n+i.margin*2?i.width/(n+i.margin*2):i.scale},e.getImageWidth=function(n,i){const s=e.getScale(n,i);return Math.floor((n+i.margin*2)*s)},e.qrToImageData=function(n,i,s){const a=i.modules.size,l=i.modules.data,o=e.getScale(a,s),d=Math.floor((a+s.margin*2)*o),c=s.margin*o,u=[s.color.light,s.color.dark];for(let h=0;h<d;h++)for(let f=0;f<d;f++){let g=(h*d+f)*4,_=s.color.light;if(h>=c&&f>=c&&h<d-c&&f<d-c){const M=Math.floor((h-c)/o),p=Math.floor((f-c)/o);_=u[l[M*a+p]?1:0]}n[g++]=_.r,n[g++]=_.g,n[g++]=_.b,n[g]=_.a}}})(rr)),rr}var fn;function Bi(){return fn||(fn=1,(function(e){const t=kn();function r(i,s,a){i.clearRect(0,0,s.width,s.height),s.style||(s.style={}),s.height=a,s.width=a,s.style.height=a+"px",s.style.width=a+"px"}function n(){try{return document.createElement("canvas")}catch{throw new Error("You need to specify a canvas element")}}e.render=function(s,a,l){let o=l,d=a;typeof o>"u"&&(!a||!a.getContext)&&(o=a,a=void 0),a||(d=n()),o=t.getOptions(o);const c=t.getImageWidth(s.modules.size,o),u=d.getContext("2d"),h=u.createImageData(c,c);return t.qrToImageData(h.data,s,o),r(u,d,c),u.putImageData(h,0,0),d},e.renderToDataURL=function(s,a,l){let o=l;typeof o>"u"&&(!a||!a.getContext)&&(o=a,a=void 0),o||(o={});const d=e.render(s,a,o),c=o.type||"image/png",u=o.rendererOpts||{};return d.toDataURL(c,u.quality)}})(tr)),tr}var nr={},mn;function Ui(){if(mn)return nr;mn=1;const e=kn();function t(i,s){const a=i.a/255,l=s+'="'+i.hex+'"';return a<1?l+" "+s+'-opacity="'+a.toFixed(2).slice(1)+'"':l}function r(i,s,a){let l=i+s;return typeof a<"u"&&(l+=" "+a),l}function n(i,s,a){let l="",o=0,d=!1,c=0;for(let u=0;u<i.length;u++){const h=Math.floor(u%s),f=Math.floor(u/s);!h&&!d&&(d=!0),i[u]?(c++,u>0&&h>0&&i[u-1]||(l+=d?r("M",h+a,.5+f+a):r("m",o,0),o=0,d=!1),h+1<s&&i[u+1]||(l+=r("h",c),c=0)):o++}return l}return nr.render=function(s,a,l){const o=e.getOptions(a),d=s.modules.size,c=s.modules.data,u=d+o.margin*2,h=o.color.light.a?"<path "+t(o.color.light,"fill")+' d="M0 0h'+u+"v"+u+'H0z"/>':"",f="<path "+t(o.color.dark,"stroke")+' d="'+n(c,d,o.margin)+'"/>',g='viewBox="0 0 '+u+" "+u+'"',M='<svg xmlns="http://www.w3.org/2000/svg" '+(o.width?'width="'+o.width+'" height="'+o.width+'" ':"")+g+' shape-rendering="crispEdges">'+h+f+`</svg>
`;return typeof l=="function"&&l(null,M),M},nr}var gn;function zi(){if(gn)return $e;gn=1;const e=Si(),t=Yi(),r=Bi(),n=Ui();function i(s,a,l,o,d){const c=[].slice.call(arguments,1),u=c.length,h=typeof c[u-1]=="function";if(!h&&!e())throw new Error("Callback required as last argument");if(h){if(u<2)throw new Error("Too few arguments provided");u===2?(d=l,l=a,a=o=void 0):u===3&&(a.getContext&&typeof d>"u"?(d=o,o=void 0):(d=o,o=l,l=a,a=void 0))}else{if(u<1)throw new Error("Too few arguments provided");return u===1?(l=a,a=o=void 0):u===2&&!a.getContext&&(o=l,l=a,a=void 0),new Promise(function(f,g){try{const _=t.create(l,o);f(s(_,a,o))}catch(_){g(_)}})}try{const f=t.create(l,o);d(null,s(f,a,o))}catch(f){d(f)}}return $e.create=t.create,$e.toCanvas=i.bind(null,r.render),$e.toDataURL=i.bind(null,r.renderToDataURL),$e.toString=i.bind(null,function(s,a,l){return n.render(s,l)}),$e}var Hi=zi();const fe=xn(Hi);var Z={},pn;function Wi(){if(pn)return Z;pn=1,Object.defineProperty(Z,"__esModule",{value:!0}),Z.Printd=Z.createIFrame=Z.createLinkStyle=Z.createStyle=void 0;var e=/^(((http[s]?)|file):)?(\/\/)+([0-9a-zA-Z-_.=?&].+)$/,t=/^((\.|\.\.)?\/)([0-9a-zA-Z-_.=?&]+\/)*([0-9a-zA-Z-_.=?&]+)$/,r=function(o){return e.test(o)||t.test(o)};function n(o,d){var c=o.createElement("style");return c.appendChild(o.createTextNode(d)),c}Z.createStyle=n;function i(o,d){var c=o.createElement("link");return c.type="text/css",c.rel="stylesheet",c.href=d,c}Z.createLinkStyle=i;function s(o){var d=window.document.createElement("iframe");return d.setAttribute("src","about:blank"),d.setAttribute("style","visibility:hidden;width:0;height:0;position:absolute;z-index:-9999;bottom:0;"),d.setAttribute("width","0"),d.setAttribute("height","0"),d.setAttribute("wmode","opaque"),o.appendChild(d),d}Z.createIFrame=s;var a={parent:window.document.body,headElements:[],bodyElements:[]},l=(function(){function o(d){this.isLoading=!1,this.hasEvents=!1,this.opts=[a,d||{}].reduce(function(c,u){return Object.keys(u).forEach(function(h){return c[h]=u[h]}),c},{}),this.iframe=s(this.opts.parent)}return o.prototype.getIFrame=function(){return this.iframe},o.prototype.print=function(d,c,u,h){if(!this.isLoading){var f=this.iframe,g=f.contentDocument,_=f.contentWindow;if(!(!g||!_)&&(this.iframe.src="about:blank",this.elCopy=d.cloneNode(!0),!!this.elCopy)){this.isLoading=!0,this.callback=h;var M=_.document;M.open(),M.write('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body></body></html>'),this.addEvents();var p=this.opts,D=p.headElements,E=p.bodyElements;Array.isArray(D)&&D.forEach(function(w){return M.head.appendChild(w)}),Array.isArray(E)&&E.forEach(function(w){return M.body.appendChild(w)}),Array.isArray(c)&&c.forEach(function(w){w&&M.head.appendChild(r(w)?i(M,w):n(M,w))}),M.body.appendChild(this.elCopy),Array.isArray(u)&&u.forEach(function(w){if(w){var B=M.createElement("script");r(w)?B.src=w:B.innerText=w,M.body.appendChild(B)}}),M.close()}}},o.prototype.printURL=function(d,c){this.isLoading||(this.addEvents(),this.isLoading=!0,this.callback=c,this.iframe.src=d)},o.prototype.onBeforePrint=function(d){this.onbeforeprint=d},o.prototype.onAfterPrint=function(d){this.onafterprint=d},o.prototype.launchPrint=function(d){this.isLoading||d.print()},o.prototype.addEvents=function(){var d=this;if(!this.hasEvents){this.hasEvents=!0,this.iframe.addEventListener("load",function(){return d.onLoad()},!1);var c=this.iframe.contentWindow;c&&(this.onbeforeprint&&c.addEventListener("beforeprint",this.onbeforeprint),this.onafterprint&&c.addEventListener("afterprint",this.onafterprint))}},o.prototype.onLoad=function(){var d=this;if(this.iframe){this.isLoading=!1;var c=this.iframe,u=c.contentDocument,h=c.contentWindow;if(!u||!h)return;typeof this.callback=="function"?this.callback({iframe:this.iframe,element:this.elCopy,launchPrint:function(){return d.launchPrint(h)}}):this.launchPrint(h)}},o})();return Z.Printd=l,Z.default=l,Z}var J=Wi(),ir,yn;function Vi(){if(yn)return ir;yn=1;class e{constructor(){this.units=["cero","uno","dos","tres","cuatro","cinco","seis","siete","ocho","nueve"],this.tenToSixteen=["diez","once","doce","trece","catorce","quince","dieciséis"],this.tens=["treinta","cuarenta","cincuenta","sesenta","setenta","ochenta","noventa"]}convertirNroMesAtexto(r){switch(typeof r=="number"&&(r=String(r)),r=this.deleteZerosLeft(r),r){case"1":return"Enero";case"2":return"Febrero";case"3":return"Marzo";case"4":return"Abril";case"5":return"Mayo";case"6":return"Junio";case"7":return"Julio";case"8":return"Agosto";case"9":return"Septiembre";case"10":return"Octubre";case"11":return"Noviembre";case"12":return"Diciembre";default:throw"Numero de mes inválido"}}convertToText(r){if(typeof r=="number"&&(r=String(r)),r=this.deleteZerosLeft(r),!this.validateNumber(r))throw"Número inválido, no se puede convertir!";return this.getName(r)}deleteZerosLeft(r){let n=0,i=!0;for(n=0;n<r.length;n++)if(r.charAt(n)!=0){i=!1;break}return i?"0":r.substr(n)}validateNumber(r){return!(isNaN(r)||r===""||r.indexOf(".")>=0||r.indexOf("-")>=0)}getName(r){return r=this.deleteZerosLeft(r),r.length===1?this.getUnits(r):r.length===2?this.getTens(r):r.length===3?this.getHundreds(r):r.length<7?this.getThousands(r):r.length<13?this.getPeriod(r,6,"millón"):r.length<19?this.getPeriod(r,12,"billón"):"Número demasiado grande."}getUnits(r){let n=parseInt(r);return this.units[n]}getTens(r){let n=r.charAt(1);if(r<17)return this.tenToSixteen[r-10];if(r<20)return"dieci"+this.getUnits(n);switch(r){case"20":return"veinte";case"22":return"veintidós";case"23":return"veintitrés";case"26":return"veintiséis"}if(r<30)return"veinti"+this.getUnits(n);let i=this.tens[r.charAt(0)-3];return n>0&&(i+=" y "+this.getUnits(n)),i}getHundreds(r){let n="",i=r.charAt(0),s=r.substr(1);if(r==100)return"cien";switch(i){case"1":n="ciento";break;case"5":n="quinientos";break;case"7":n="setecientos";break;case"9":n="novecientos"}return n===""&&(n=this.getUnits(i)+"cientos"),s>0&&(n+=" "+this.getName(s)),n}getThousands(r){let n="mil",i=r.length-3,s=r.substr(0,i),a=r.substr(i);return s>1&&(n=this.getName(s).replace("uno","un")+" mil"),a>0&&(n+=" "+this.getName(a)),n}getPeriod(r,n,i){let s="un "+i,a=r.length-n,l=r.substr(0,a),o=r.substr(a);return l>1&&(s=this.getName(l).replace("uno","un")+" "+i.replace("ó","o")+"es"),o>0&&(s+=" "+this.getName(o)),s}}return ir={conversorNumerosALetras:e},ir}var ji=Vi();const me=xn(ji);var ct={},vn;function qi(){if(vn)return ct;vn=1,Object.defineProperty(ct,"__esModule",{value:!0});function e(o){switch(o){case 1:return"Un";case 2:return"Dos";case 3:return"Tres";case 4:return"Cuatro";case 5:return"Cinco";case 6:return"Seis";case 7:return"Siete";case 8:return"Ocho";case 9:return"Nueve";default:return""}}function t(o,d){return d>0?o+" y "+e(d):o}function r(o){var d=Math.floor(o/10),c=o-d*10;switch(d){case 1:switch(c){case 0:return"Diez";case 1:return"Once";case 2:return"Doce";case 3:return"Trece";case 4:return"Catorce";case 5:return"Quince";default:return"Dieci"+e(c).toLowerCase()}case 2:return c===0?"Veinte":"Veinti"+e(c).toLowerCase();case 3:return t("Treinta",c);case 4:return t("Cuarenta",c);case 5:return t("Cincuenta",c);case 6:return t("Sesenta",c);case 7:return t("Setenta",c);case 8:return t("Ochenta",c);case 9:return t("Noventa",c);case 0:return e(c);default:return""}}function n(o){var d=Math.floor(o/100),c=o-d*100;switch(d){case 1:return c>0?"Ciento "+r(c):"Cien";case 2:return"Doscientos "+r(c);case 3:return"Trescientos "+r(c);case 4:return"Cuatrocientos "+r(c);case 5:return"Quinientos "+r(c);case 6:return"Seiscientos "+r(c);case 7:return"Setecientos "+r(c);case 8:return"Ochocientos "+r(c);case 9:return"Novecientos "+r(c);default:return r(c)}}function i(o,d,c,u){var h=Math.floor(o/d),f=o-h*d,g="";return h>0&&(h>1?g=n(h)+" "+u:g=c),f>0&&(g+=""),g}function s(o){var d=1e3,c=Math.floor(o/d),u=o-c*d,h=i(o,d,"Un Mil","Mil"),f=n(u);return h===""?f:(h+" "+f).trim()}function a(o){var d=1e6,c=Math.floor(o/d),u=o-c*d,h=i(o,d,"Un Millón de","Millones de"),f=s(u);return h===""?f:(h+" "+f).trim()}function l(o){var d={enteros:Math.floor(o),centavos:Math.round(o*100)-Math.floor(o)*100,letrasCentavos:"",letrasMonedaPlural:"Pesos",letrasMonedaSingular:"Peso",letrasMonedaCentavoPlural:"/100 M.N.",letrasMonedaCentavoSingular:"/100 M.N."};return d.centavos>=0&&(d.letrasCentavos=(function(){return d.centavos>=1&d.centavos<=9?"0"+d.centavos+d.letrasMonedaCentavoSingular:d.centavos===0?"00"+d.letrasMonedaCentavoSingular:d.centavos+d.letrasMonedaCentavoPlural})()),d.enteros===0?("Cero "+d.letrasMonedaPlural+" "+d.letrasCentavos).trim():d.enteros===1?(a(d.enteros)+" "+d.letrasMonedaSingular+" "+d.letrasCentavos).trim():(a(d.enteros)+" "+d.letrasMonedaPlural+" "+d.letrasCentavos).trim()}return ct.NumerosALetras=l,ct}qi();var Nn;function y(){return Nn.apply(null,arguments)}function Gi(e){Nn=e}function te(e){return e instanceof Array||Object.prototype.toString.call(e)==="[object Array]"}function Oe(e){return e!=null&&Object.prototype.toString.call(e)==="[object Object]"}function L(e,t){return Object.prototype.hasOwnProperty.call(e,t)}function yr(e){if(Object.getOwnPropertyNames)return Object.getOwnPropertyNames(e).length===0;var t;for(t in e)if(L(e,t))return!1;return!0}function q(e){return e===void 0}function _e(e){return typeof e=="number"||Object.prototype.toString.call(e)==="[object Number]"}function rt(e){return e instanceof Date||Object.prototype.toString.call(e)==="[object Date]"}function On(e,t){var r=[],n,i=e.length;for(n=0;n<i;++n)r.push(t(e[n],n));return r}function xe(e,t){for(var r in t)L(t,r)&&(e[r]=t[r]);return L(t,"toString")&&(e.toString=t.toString),L(t,"valueOf")&&(e.valueOf=t.valueOf),e}function de(e,t,r,n){return ei(e,t,r,n,!0).utc()}function Zi(){return{empty:!1,unusedTokens:[],unusedInput:[],overflow:-2,charsLeftOver:0,nullInput:!1,invalidEra:null,invalidMonth:null,invalidFormat:!1,userInvalidated:!1,iso:!1,parsedDateParts:[],era:null,meridiem:null,rfc2822:!1,weekdayMismatch:!1}}function R(e){return e._pf==null&&(e._pf=Zi()),e._pf}var dr;Array.prototype.some?dr=Array.prototype.some:dr=function(e){var t=Object(this),r=t.length>>>0,n;for(n=0;n<r;n++)if(n in t&&e.call(this,t[n],n,t))return!0;return!1};function vr(e){var t=null,r=!1,n=e._d&&!isNaN(e._d.getTime());if(n&&(t=R(e),r=dr.call(t.parsedDateParts,function(i){return i!=null}),n=t.overflow<0&&!t.empty&&!t.invalidEra&&!t.invalidMonth&&!t.invalidWeekday&&!t.weekdayMismatch&&!t.nullInput&&!t.invalidFormat&&!t.userInvalidated&&(!t.meridiem||t.meridiem&&r),e._strict&&(n=n&&t.charsLeftOver===0&&t.unusedTokens.length===0&&t.bigHour===void 0)),Object.isFrozen==null||!Object.isFrozen(e))e._isValid=n;else return n;return e._isValid}function _t(e){var t=de(NaN);return e!=null?xe(R(t),e):R(t).userInvalidated=!0,t}var bn=y.momentProperties=[],sr=!1;function br(e,t){var r,n,i,s=bn.length;if(q(t._isAMomentObject)||(e._isAMomentObject=t._isAMomentObject),q(t._i)||(e._i=t._i),q(t._f)||(e._f=t._f),q(t._l)||(e._l=t._l),q(t._strict)||(e._strict=t._strict),q(t._tzm)||(e._tzm=t._tzm),q(t._isUTC)||(e._isUTC=t._isUTC),q(t._offset)||(e._offset=t._offset),q(t._pf)||(e._pf=R(t)),q(t._locale)||(e._locale=t._locale),s>0)for(r=0;r<s;r++)n=bn[r],i=t[n],q(i)||(e[n]=i);return e}function nt(e){br(this,e),this._d=new Date(e._d!=null?e._d.getTime():NaN),this.isValid()||(this._d=new Date(NaN)),sr===!1&&(sr=!0,y.updateOffset(this),sr=!1)}function re(e){return e instanceof nt||e!=null&&e._isAMomentObject!=null}function An(e){y.suppressDeprecationWarnings===!1&&typeof console<"u"&&console.warn&&console.warn("Deprecation warning: "+e)}function Q(e,t){var r=!0;return xe(function(){if(y.deprecationHandler!=null&&y.deprecationHandler(null,e),r){var n=[],i,s,a,l=arguments.length;for(s=0;s<l;s++){if(i="",typeof arguments[s]=="object"){i+=`
[`+s+"] ";for(a in arguments[0])L(arguments[0],a)&&(i+=a+": "+arguments[0][a]+", ");i=i.slice(0,-2)}else i=arguments[s];n.push(i)}An(e+`
Arguments: `+Array.prototype.slice.call(n).join("")+`
`+new Error().stack),r=!1}return t.apply(this,arguments)},t)}var wn={};function Fn(e,t){y.deprecationHandler!=null&&y.deprecationHandler(e,t),wn[e]||(An(t),wn[e]=!0)}y.suppressDeprecationWarnings=!1;y.deprecationHandler=null;function ce(e){return typeof Function<"u"&&e instanceof Function||Object.prototype.toString.call(e)==="[object Function]"}function Ji(e){var t,r;for(r in e)L(e,r)&&(t=e[r],ce(t)?this[r]=t:this["_"+r]=t);this._config=e,this._dayOfMonthOrdinalParseLenient=new RegExp((this._dayOfMonthOrdinalParse.source||this._ordinalParse.source)+"|"+/\d{1,2}/.source)}function cr(e,t){var r=xe({},e),n;for(n in t)L(t,n)&&(Oe(e[n])&&Oe(t[n])?(r[n]={},xe(r[n],e[n]),xe(r[n],t[n])):t[n]!=null?r[n]=t[n]:delete r[n]);for(n in e)L(e,n)&&!L(t,n)&&Oe(e[n])&&(r[n]=xe({},r[n]));return r}function wr(e){e!=null&&this.set(e)}var ur;Object.keys?ur=Object.keys:ur=function(e){var t,r=[];for(t in e)L(e,t)&&r.push(t);return r};var Ki={sameDay:"[Today at] LT",nextDay:"[Tomorrow at] LT",nextWeek:"dddd [at] LT",lastDay:"[Yesterday at] LT",lastWeek:"[Last] dddd [at] LT",sameElse:"L"};function Qi(e,t,r){var n=this._calendar[e]||this._calendar.sameElse;return ce(n)?n.call(t,r):n}function le(e,t,r){var n=""+Math.abs(e),i=t-n.length,s=e>=0;return(s?r?"+":"":"-")+Math.pow(10,Math.max(0,i)).toString().substr(1)+n}var _r=/(\[[^\[]*\])|(\\)?([Hh]mm(ss)?|Mo|MM?M?M?|Do|DDDo|DD?D?D?|ddd?d?|do?|w[o|w]?|W[o|W]?|Qo?|N{1,5}|YYYYYY|YYYYY|YYYY|YY|y{2,4}|yo?|gg(ggg?)?|GG(GGG?)?|e|E|a|A|hh?|HH?|kk?|mm?|ss?|S{1,9}|x|X|zz?|ZZ?|.)/g,ut=/(\[[^\[]*\])|(\\)?(LTS|LT|LL?L?L?|l{1,4})/g,ar={},Be={};function x(e,t,r,n){var i=n;typeof n=="string"&&(i=function(){return this[n]()}),e&&(Be[e]=i),t&&(Be[t[0]]=function(){return le(i.apply(this,arguments),t[1],t[2])}),r&&(Be[r]=function(){return this.localeData().ordinal(i.apply(this,arguments),e)})}function Xi(e){return e.match(/\[[\s\S]/)?e.replace(/^\[|\]$/g,""):e.replace(/\\/g,"")}function es(e){var t=e.match(_r),r,n;for(r=0,n=t.length;r<n;r++)Be[t[r]]?t[r]=Be[t[r]]:t[r]=Xi(t[r]);return function(i){var s="",a;for(a=0;a<n;a++)s+=ce(t[a])?t[a].call(i,e):t[a];return s}}function ft(e,t){return e.isValid()?(t=Rn(t,e.localeData()),ar[t]=ar[t]||es(t),ar[t](e)):e.localeData().invalidDate()}function Rn(e,t){var r=5;function n(i){return t.longDateFormat(i)||i}for(ut.lastIndex=0;r>=0&&ut.test(e);)e=e.replace(ut,n),ut.lastIndex=0,r-=1;return e}var ts={LTS:"h:mm:ss A",LT:"h:mm A",L:"MM/DD/YYYY",LL:"MMMM D, YYYY",LLL:"MMMM D, YYYY h:mm A",LLLL:"dddd, MMMM D, YYYY h:mm A"};function rs(e){var t=this._longDateFormat[e],r=this._longDateFormat[e.toUpperCase()];return t||!r?t:(this._longDateFormat[e]=r.match(_r).map(function(n){return n==="MMMM"||n==="MM"||n==="DD"||n==="dddd"?n.slice(1):n}).join(""),this._longDateFormat[e])}var ns="Invalid date";function is(){return this._invalidDate}var ss="%d",as=/\d{1,2}/;function os(e){return this._ordinal.replace("%d",e)}var ls={future:"in %s",past:"%s ago",s:"a few seconds",ss:"%d seconds",m:"a minute",mm:"%d minutes",h:"an hour",hh:"%d hours",d:"a day",dd:"%d days",w:"a week",ww:"%d weeks",M:"a month",MM:"%d months",y:"a year",yy:"%d years"};function ds(e,t,r,n){var i=this._relativeTime[r];return ce(i)?i(e,t,r,n):i.replace(/%d/i,e)}function cs(e,t){var r=this._relativeTime[e>0?"future":"past"];return ce(r)?r(t):r.replace(/%s/i,t)}var _n={D:"date",dates:"date",date:"date",d:"day",days:"day",day:"day",e:"weekday",weekdays:"weekday",weekday:"weekday",E:"isoWeekday",isoweekdays:"isoWeekday",isoweekday:"isoWeekday",DDD:"dayOfYear",dayofyears:"dayOfYear",dayofyear:"dayOfYear",h:"hour",hours:"hour",hour:"hour",ms:"millisecond",milliseconds:"millisecond",millisecond:"millisecond",m:"minute",minutes:"minute",minute:"minute",M:"month",months:"month",month:"month",Q:"quarter",quarters:"quarter",quarter:"quarter",s:"second",seconds:"second",second:"second",gg:"weekYear",weekyears:"weekYear",weekyear:"weekYear",GG:"isoWeekYear",isoweekyears:"isoWeekYear",isoweekyear:"isoWeekYear",w:"week",weeks:"week",week:"week",W:"isoWeek",isoweeks:"isoWeek",isoweek:"isoWeek",y:"year",years:"year",year:"year"};function X(e){return typeof e=="string"?_n[e]||_n[e.toLowerCase()]:void 0}function Sr(e){var t={},r,n;for(n in e)L(e,n)&&(r=X(n),r&&(t[r]=e[n]));return t}var us={date:9,day:11,weekday:11,isoWeekday:11,dayOfYear:4,hour:13,millisecond:16,minute:14,month:8,quarter:7,second:15,weekYear:1,isoWeekYear:1,week:5,isoWeek:5,year:1};function hs(e){var t=[],r;for(r in e)L(e,r)&&t.push({unit:r,priority:us[r]});return t.sort(function(n,i){return n.priority-i.priority}),t}var Pn=/\d/,G=/\d\d/,$n=/\d{3}/,Mr=/\d{4}/,St=/[+-]?\d{6}/,z=/\d\d?/,Ln=/\d\d\d\d?/,In=/\d\d\d\d\d\d?/,Mt=/\d{1,3}/,Tr=/\d{1,4}/,Tt=/[+-]?\d{1,6}/,He=/\d+/,xt=/[+-]?\d+/,fs=/Z|[+-]\d\d:?\d\d/gi,Dt=/Z|[+-]\d\d(?::?\d\d)?/gi,ms=/[+-]?\d+(\.\d{1,3})?/,it=/[0-9]{0,256}['a-z\u00A0-\u05FF\u0700-\uD7FF\uF900-\uFDCF\uFDF0-\uFF07\uFF10-\uFFEF]{1,256}|[\u0600-\u06FF\/]{1,256}(\s*?[\u0600-\u06FF]{1,256}){1,2}/i,We=/^[1-9]\d?/,xr=/^([1-9]\d|\d)/,pt;pt={};function b(e,t,r){pt[e]=ce(t)?t:function(n,i){return n&&r?r:t}}function gs(e,t){return L(pt,e)?pt[e](t._strict,t._locale):new RegExp(ps(e))}function ps(e){return be(e.replace("\\","").replace(/\\(\[)|\\(\])|\[([^\]\[]*)\]|\\(.)/g,function(t,r,n,i,s){return r||n||i||s}))}function be(e){return e.replace(/[-\/\\^$*+?.()|[\]{}]/g,"\\$&")}function K(e){return e<0?Math.ceil(e)||0:Math.floor(e)}function P(e){var t=+e,r=0;return t!==0&&isFinite(t)&&(r=K(t)),r}var hr={};function Y(e,t){var r,n=t,i;for(typeof e=="string"&&(e=[e]),_e(t)&&(n=function(s,a){a[t]=P(s)}),i=e.length,r=0;r<i;r++)hr[e[r]]=n}function st(e,t){Y(e,function(r,n,i,s){i._w=i._w||{},t(r,i._w,i,s)})}function ys(e,t,r){t!=null&&L(hr,e)&&hr[e](t,r._a,r,e)}function Ct(e){return e%4===0&&e%100!==0||e%400===0}var j=0,ye=1,oe=2,V=3,ee=4,ve=5,Ne=6,vs=7,bs=8;x("Y",0,0,function(){var e=this.year();return e<=9999?le(e,4):"+"+e});x(0,["YY",2],0,function(){return this.year()%100});x(0,["YYYY",4],0,"year");x(0,["YYYYY",5],0,"year");x(0,["YYYYYY",6,!0],0,"year");b("Y",xt);b("YY",z,G);b("YYYY",Tr,Mr);b("YYYYY",Tt,St);b("YYYYYY",Tt,St);Y(["YYYYY","YYYYYY"],j);Y("YYYY",function(e,t){t[j]=e.length===2?y.parseTwoDigitYear(e):P(e)});Y("YY",function(e,t){t[j]=y.parseTwoDigitYear(e)});Y("Y",function(e,t){t[j]=parseInt(e,10)});function Ke(e){return Ct(e)?366:365}y.parseTwoDigitYear=function(e){return P(e)+(P(e)>68?1900:2e3)};var Yn=Ve("FullYear",!0);function ws(){return Ct(this.year())}function Ve(e,t){return function(r){return r!=null?(Bn(this,e,r),y.updateOffset(this,t),this):Qe(this,e)}}function Qe(e,t){if(!e.isValid())return NaN;var r=e._d,n=e._isUTC;switch(t){case"Milliseconds":return n?r.getUTCMilliseconds():r.getMilliseconds();case"Seconds":return n?r.getUTCSeconds():r.getSeconds();case"Minutes":return n?r.getUTCMinutes():r.getMinutes();case"Hours":return n?r.getUTCHours():r.getHours();case"Date":return n?r.getUTCDate():r.getDate();case"Day":return n?r.getUTCDay():r.getDay();case"Month":return n?r.getUTCMonth():r.getMonth();case"FullYear":return n?r.getUTCFullYear():r.getFullYear();default:return NaN}}function Bn(e,t,r){var n,i,s,a,l;if(!(!e.isValid()||isNaN(r))){switch(n=e._d,i=e._isUTC,t){case"Milliseconds":return void(i?n.setUTCMilliseconds(r):n.setMilliseconds(r));case"Seconds":return void(i?n.setUTCSeconds(r):n.setSeconds(r));case"Minutes":return void(i?n.setUTCMinutes(r):n.setMinutes(r));case"Hours":return void(i?n.setUTCHours(r):n.setHours(r));case"Date":return void(i?n.setUTCDate(r):n.setDate(r));case"FullYear":break;default:return}s=r,a=e.month(),l=e.date(),l=l===29&&a===1&&!Ct(s)?28:l,i?n.setUTCFullYear(s,a,l):n.setFullYear(s,a,l)}}function _s(e){return e=X(e),ce(this[e])?this[e]():this}function Ss(e,t){if(typeof e=="object"){e=Sr(e);var r=hs(e),n,i=r.length;for(n=0;n<i;n++)this[r[n].unit](e[r[n].unit])}else if(e=X(e),ce(this[e]))return this[e](t);return this}function Ms(e,t){return(e%t+t)%t}var W;Array.prototype.indexOf?W=Array.prototype.indexOf:W=function(e){var t;for(t=0;t<this.length;++t)if(this[t]===e)return t;return-1};function Dr(e,t){if(isNaN(e)||isNaN(t))return NaN;var r=Ms(t,12);return e+=(t-r)/12,r===1?Ct(e)?29:28:31-r%7%2}x("M",["MM",2],"Mo",function(){return this.month()+1});x("MMM",0,0,function(e){return this.localeData().monthsShort(this,e)});x("MMMM",0,0,function(e){return this.localeData().months(this,e)});b("M",z,We);b("MM",z,G);b("MMM",function(e,t){return t.monthsShortRegex(e)});b("MMMM",function(e,t){return t.monthsRegex(e)});Y(["M","MM"],function(e,t){t[ye]=P(e)-1});Y(["MMM","MMMM"],function(e,t,r,n){var i=r._locale.monthsParse(e,n,r._strict);i!=null?t[ye]=i:R(r).invalidMonth=e});var Ts="January_February_March_April_May_June_July_August_September_October_November_December".split("_"),Un="Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec".split("_"),zn=/D[oD]?(\[[^\[\]]*\]|\s)+MMMM?/,xs=it,Ds=it;function Cs(e,t){return e?te(this._months)?this._months[e.month()]:this._months[(this._months.isFormat||zn).test(t)?"format":"standalone"][e.month()]:te(this._months)?this._months:this._months.standalone}function Es(e,t){return e?te(this._monthsShort)?this._monthsShort[e.month()]:this._monthsShort[zn.test(t)?"format":"standalone"][e.month()]:te(this._monthsShort)?this._monthsShort:this._monthsShort.standalone}function ks(e,t,r){var n,i,s,a=e.toLocaleLowerCase();if(!this._monthsParse)for(this._monthsParse=[],this._longMonthsParse=[],this._shortMonthsParse=[],n=0;n<12;++n)s=de([2e3,n]),this._shortMonthsParse[n]=this.monthsShort(s,"").toLocaleLowerCase(),this._longMonthsParse[n]=this.months(s,"").toLocaleLowerCase();return r?t==="MMM"?(i=W.call(this._shortMonthsParse,a),i!==-1?i:null):(i=W.call(this._longMonthsParse,a),i!==-1?i:null):t==="MMM"?(i=W.call(this._shortMonthsParse,a),i!==-1?i:(i=W.call(this._longMonthsParse,a),i!==-1?i:null)):(i=W.call(this._longMonthsParse,a),i!==-1?i:(i=W.call(this._shortMonthsParse,a),i!==-1?i:null))}function Ns(e,t,r){var n,i,s;if(this._monthsParseExact)return ks.call(this,e,t,r);for(this._monthsParse||(this._monthsParse=[],this._longMonthsParse=[],this._shortMonthsParse=[]),n=0;n<12;n++){if(i=de([2e3,n]),r&&!this._longMonthsParse[n]&&(this._longMonthsParse[n]=new RegExp("^"+this.months(i,"").replace(".","")+"$","i"),this._shortMonthsParse[n]=new RegExp("^"+this.monthsShort(i,"").replace(".","")+"$","i")),!r&&!this._monthsParse[n]&&(s="^"+this.months(i,"")+"|^"+this.monthsShort(i,""),this._monthsParse[n]=new RegExp(s.replace(".",""),"i")),r&&t==="MMMM"&&this._longMonthsParse[n].test(e))return n;if(r&&t==="MMM"&&this._shortMonthsParse[n].test(e))return n;if(!r&&this._monthsParse[n].test(e))return n}}function Hn(e,t){if(!e.isValid())return e;if(typeof t=="string"){if(/^\d+$/.test(t))t=P(t);else if(t=e.localeData().monthsParse(t),!_e(t))return e}var r=t,n=e.date();return n=n<29?n:Math.min(n,Dr(e.year(),r)),e._isUTC?e._d.setUTCMonth(r,n):e._d.setMonth(r,n),e}function Wn(e){return e!=null?(Hn(this,e),y.updateOffset(this,!0),this):Qe(this,"Month")}function Os(){return Dr(this.year(),this.month())}function As(e){return this._monthsParseExact?(L(this,"_monthsRegex")||Vn.call(this),e?this._monthsShortStrictRegex:this._monthsShortRegex):(L(this,"_monthsShortRegex")||(this._monthsShortRegex=xs),this._monthsShortStrictRegex&&e?this._monthsShortStrictRegex:this._monthsShortRegex)}function Fs(e){return this._monthsParseExact?(L(this,"_monthsRegex")||Vn.call(this),e?this._monthsStrictRegex:this._monthsRegex):(L(this,"_monthsRegex")||(this._monthsRegex=Ds),this._monthsStrictRegex&&e?this._monthsStrictRegex:this._monthsRegex)}function Vn(){function e(o,d){return d.length-o.length}var t=[],r=[],n=[],i,s,a,l;for(i=0;i<12;i++)s=de([2e3,i]),a=be(this.monthsShort(s,"")),l=be(this.months(s,"")),t.push(a),r.push(l),n.push(l),n.push(a);t.sort(e),r.sort(e),n.sort(e),this._monthsRegex=new RegExp("^("+n.join("|")+")","i"),this._monthsShortRegex=this._monthsRegex,this._monthsStrictRegex=new RegExp("^("+r.join("|")+")","i"),this._monthsShortStrictRegex=new RegExp("^("+t.join("|")+")","i")}function Rs(e,t,r,n,i,s,a){var l;return e<100&&e>=0?(l=new Date(e+400,t,r,n,i,s,a),isFinite(l.getFullYear())&&l.setFullYear(e)):l=new Date(e,t,r,n,i,s,a),l}function Xe(e){var t,r;return e<100&&e>=0?(r=Array.prototype.slice.call(arguments),r[0]=e+400,t=new Date(Date.UTC.apply(null,r)),isFinite(t.getUTCFullYear())&&t.setUTCFullYear(e)):t=new Date(Date.UTC.apply(null,arguments)),t}function yt(e,t,r){var n=7+t-r,i=(7+Xe(e,0,n).getUTCDay()-t)%7;return-i+n-1}function jn(e,t,r,n,i){var s=(7+r-n)%7,a=yt(e,n,i),l=1+7*(t-1)+s+a,o,d;return l<=0?(o=e-1,d=Ke(o)+l):l>Ke(e)?(o=e+1,d=l-Ke(e)):(o=e,d=l),{year:o,dayOfYear:d}}function et(e,t,r){var n=yt(e.year(),t,r),i=Math.floor((e.dayOfYear()-n-1)/7)+1,s,a;return i<1?(a=e.year()-1,s=i+we(a,t,r)):i>we(e.year(),t,r)?(s=i-we(e.year(),t,r),a=e.year()+1):(a=e.year(),s=i),{week:s,year:a}}function we(e,t,r){var n=yt(e,t,r),i=yt(e+1,t,r);return(Ke(e)-n+i)/7}x("w",["ww",2],"wo","week");x("W",["WW",2],"Wo","isoWeek");b("w",z,We);b("ww",z,G);b("W",z,We);b("WW",z,G);st(["w","ww","W","WW"],function(e,t,r,n){t[n.substr(0,1)]=P(e)});function Ps(e){return et(e,this._week.dow,this._week.doy).week}var $s={dow:0,doy:6};function Ls(){return this._week.dow}function Is(){return this._week.doy}function Ys(e){var t=this.localeData().week(this);return e==null?t:this.add((e-t)*7,"d")}function Bs(e){var t=et(this,1,4).week;return e==null?t:this.add((e-t)*7,"d")}x("d",0,"do","day");x("dd",0,0,function(e){return this.localeData().weekdaysMin(this,e)});x("ddd",0,0,function(e){return this.localeData().weekdaysShort(this,e)});x("dddd",0,0,function(e){return this.localeData().weekdays(this,e)});x("e",0,0,"weekday");x("E",0,0,"isoWeekday");b("d",z);b("e",z);b("E",z);b("dd",function(e,t){return t.weekdaysMinRegex(e)});b("ddd",function(e,t){return t.weekdaysShortRegex(e)});b("dddd",function(e,t){return t.weekdaysRegex(e)});st(["dd","ddd","dddd"],function(e,t,r,n){var i=r._locale.weekdaysParse(e,n,r._strict);i!=null?t.d=i:R(r).invalidWeekday=e});st(["d","e","E"],function(e,t,r,n){t[n]=P(e)});function Us(e,t){return typeof e!="string"?e:isNaN(e)?(e=t.weekdaysParse(e),typeof e=="number"?e:null):parseInt(e,10)}function zs(e,t){return typeof e=="string"?t.weekdaysParse(e)%7||7:isNaN(e)?null:e}function Cr(e,t){return e.slice(t,7).concat(e.slice(0,t))}var Hs="Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),qn="Sun_Mon_Tue_Wed_Thu_Fri_Sat".split("_"),Ws="Su_Mo_Tu_We_Th_Fr_Sa".split("_"),Vs=it,js=it,qs=it;function Gs(e,t){var r=te(this._weekdays)?this._weekdays:this._weekdays[e&&e!==!0&&this._weekdays.isFormat.test(t)?"format":"standalone"];return e===!0?Cr(r,this._week.dow):e?r[e.day()]:r}function Zs(e){return e===!0?Cr(this._weekdaysShort,this._week.dow):e?this._weekdaysShort[e.day()]:this._weekdaysShort}function Js(e){return e===!0?Cr(this._weekdaysMin,this._week.dow):e?this._weekdaysMin[e.day()]:this._weekdaysMin}function Ks(e,t,r){var n,i,s,a=e.toLocaleLowerCase();if(!this._weekdaysParse)for(this._weekdaysParse=[],this._shortWeekdaysParse=[],this._minWeekdaysParse=[],n=0;n<7;++n)s=de([2e3,1]).day(n),this._minWeekdaysParse[n]=this.weekdaysMin(s,"").toLocaleLowerCase(),this._shortWeekdaysParse[n]=this.weekdaysShort(s,"").toLocaleLowerCase(),this._weekdaysParse[n]=this.weekdays(s,"").toLocaleLowerCase();return r?t==="dddd"?(i=W.call(this._weekdaysParse,a),i!==-1?i:null):t==="ddd"?(i=W.call(this._shortWeekdaysParse,a),i!==-1?i:null):(i=W.call(this._minWeekdaysParse,a),i!==-1?i:null):t==="dddd"?(i=W.call(this._weekdaysParse,a),i!==-1||(i=W.call(this._shortWeekdaysParse,a),i!==-1)?i:(i=W.call(this._minWeekdaysParse,a),i!==-1?i:null)):t==="ddd"?(i=W.call(this._shortWeekdaysParse,a),i!==-1||(i=W.call(this._weekdaysParse,a),i!==-1)?i:(i=W.call(this._minWeekdaysParse,a),i!==-1?i:null)):(i=W.call(this._minWeekdaysParse,a),i!==-1||(i=W.call(this._weekdaysParse,a),i!==-1)?i:(i=W.call(this._shortWeekdaysParse,a),i!==-1?i:null))}function Qs(e,t,r){var n,i,s;if(this._weekdaysParseExact)return Ks.call(this,e,t,r);for(this._weekdaysParse||(this._weekdaysParse=[],this._minWeekdaysParse=[],this._shortWeekdaysParse=[],this._fullWeekdaysParse=[]),n=0;n<7;n++){if(i=de([2e3,1]).day(n),r&&!this._fullWeekdaysParse[n]&&(this._fullWeekdaysParse[n]=new RegExp("^"+this.weekdays(i,"").replace(".","\\.?")+"$","i"),this._shortWeekdaysParse[n]=new RegExp("^"+this.weekdaysShort(i,"").replace(".","\\.?")+"$","i"),this._minWeekdaysParse[n]=new RegExp("^"+this.weekdaysMin(i,"").replace(".","\\.?")+"$","i")),this._weekdaysParse[n]||(s="^"+this.weekdays(i,"")+"|^"+this.weekdaysShort(i,"")+"|^"+this.weekdaysMin(i,""),this._weekdaysParse[n]=new RegExp(s.replace(".",""),"i")),r&&t==="dddd"&&this._fullWeekdaysParse[n].test(e))return n;if(r&&t==="ddd"&&this._shortWeekdaysParse[n].test(e))return n;if(r&&t==="dd"&&this._minWeekdaysParse[n].test(e))return n;if(!r&&this._weekdaysParse[n].test(e))return n}}function Xs(e){if(!this.isValid())return e!=null?this:NaN;var t=Qe(this,"Day");return e!=null?(e=Us(e,this.localeData()),this.add(e-t,"d")):t}function ea(e){if(!this.isValid())return e!=null?this:NaN;var t=(this.day()+7-this.localeData()._week.dow)%7;return e==null?t:this.add(e-t,"d")}function ta(e){if(!this.isValid())return e!=null?this:NaN;if(e!=null){var t=zs(e,this.localeData());return this.day(this.day()%7?t:t-7)}else return this.day()||7}function ra(e){return this._weekdaysParseExact?(L(this,"_weekdaysRegex")||Er.call(this),e?this._weekdaysStrictRegex:this._weekdaysRegex):(L(this,"_weekdaysRegex")||(this._weekdaysRegex=Vs),this._weekdaysStrictRegex&&e?this._weekdaysStrictRegex:this._weekdaysRegex)}function na(e){return this._weekdaysParseExact?(L(this,"_weekdaysRegex")||Er.call(this),e?this._weekdaysShortStrictRegex:this._weekdaysShortRegex):(L(this,"_weekdaysShortRegex")||(this._weekdaysShortRegex=js),this._weekdaysShortStrictRegex&&e?this._weekdaysShortStrictRegex:this._weekdaysShortRegex)}function ia(e){return this._weekdaysParseExact?(L(this,"_weekdaysRegex")||Er.call(this),e?this._weekdaysMinStrictRegex:this._weekdaysMinRegex):(L(this,"_weekdaysMinRegex")||(this._weekdaysMinRegex=qs),this._weekdaysMinStrictRegex&&e?this._weekdaysMinStrictRegex:this._weekdaysMinRegex)}function Er(){function e(c,u){return u.length-c.length}var t=[],r=[],n=[],i=[],s,a,l,o,d;for(s=0;s<7;s++)a=de([2e3,1]).day(s),l=be(this.weekdaysMin(a,"")),o=be(this.weekdaysShort(a,"")),d=be(this.weekdays(a,"")),t.push(l),r.push(o),n.push(d),i.push(l),i.push(o),i.push(d);t.sort(e),r.sort(e),n.sort(e),i.sort(e),this._weekdaysRegex=new RegExp("^("+i.join("|")+")","i"),this._weekdaysShortRegex=this._weekdaysRegex,this._weekdaysMinRegex=this._weekdaysRegex,this._weekdaysStrictRegex=new RegExp("^("+n.join("|")+")","i"),this._weekdaysShortStrictRegex=new RegExp("^("+r.join("|")+")","i"),this._weekdaysMinStrictRegex=new RegExp("^("+t.join("|")+")","i")}function kr(){return this.hours()%12||12}function sa(){return this.hours()||24}x("H",["HH",2],0,"hour");x("h",["hh",2],0,kr);x("k",["kk",2],0,sa);x("hmm",0,0,function(){return""+kr.apply(this)+le(this.minutes(),2)});x("hmmss",0,0,function(){return""+kr.apply(this)+le(this.minutes(),2)+le(this.seconds(),2)});x("Hmm",0,0,function(){return""+this.hours()+le(this.minutes(),2)});x("Hmmss",0,0,function(){return""+this.hours()+le(this.minutes(),2)+le(this.seconds(),2)});function Gn(e,t){x(e,0,0,function(){return this.localeData().meridiem(this.hours(),this.minutes(),t)})}Gn("a",!0);Gn("A",!1);function Zn(e,t){return t._meridiemParse}b("a",Zn);b("A",Zn);b("H",z,xr);b("h",z,We);b("k",z,We);b("HH",z,G);b("hh",z,G);b("kk",z,G);b("hmm",Ln);b("hmmss",In);b("Hmm",Ln);b("Hmmss",In);Y(["H","HH"],V);Y(["k","kk"],function(e,t,r){var n=P(e);t[V]=n===24?0:n});Y(["a","A"],function(e,t,r){r._isPm=r._locale.isPM(e),r._meridiem=e});Y(["h","hh"],function(e,t,r){t[V]=P(e),R(r).bigHour=!0});Y("hmm",function(e,t,r){var n=e.length-2;t[V]=P(e.substr(0,n)),t[ee]=P(e.substr(n)),R(r).bigHour=!0});Y("hmmss",function(e,t,r){var n=e.length-4,i=e.length-2;t[V]=P(e.substr(0,n)),t[ee]=P(e.substr(n,2)),t[ve]=P(e.substr(i)),R(r).bigHour=!0});Y("Hmm",function(e,t,r){var n=e.length-2;t[V]=P(e.substr(0,n)),t[ee]=P(e.substr(n))});Y("Hmmss",function(e,t,r){var n=e.length-4,i=e.length-2;t[V]=P(e.substr(0,n)),t[ee]=P(e.substr(n,2)),t[ve]=P(e.substr(i))});function aa(e){return(e+"").toLowerCase().charAt(0)==="p"}var oa=/[ap]\.?m?\.?/i,la=Ve("Hours",!0);function da(e,t,r){return e>11?r?"pm":"PM":r?"am":"AM"}var Jn={calendar:Ki,longDateFormat:ts,invalidDate:ns,ordinal:ss,dayOfMonthOrdinalParse:as,relativeTime:ls,months:Ts,monthsShort:Un,week:$s,weekdays:Hs,weekdaysMin:Ws,weekdaysShort:qn,meridiemParse:oa},H={},Ze={},tt;function ca(e,t){var r,n=Math.min(e.length,t.length);for(r=0;r<n;r+=1)if(e[r]!==t[r])return r;return n}function Sn(e){return e&&e.toLowerCase().replace("_","-")}function ua(e){for(var t=0,r,n,i,s;t<e.length;){for(s=Sn(e[t]).split("-"),r=s.length,n=Sn(e[t+1]),n=n?n.split("-"):null;r>0;){if(i=Et(s.slice(0,r).join("-")),i)return i;if(n&&n.length>=r&&ca(s,n)>=r-1)break;r--}t++}return tt}function ha(e){return!!(e&&e.match("^[^/\\\\]*$"))}function Et(e){var t=null,r;if(H[e]===void 0&&typeof module<"u"&&module&&module.exports&&ha(e))try{t=tt._abbr,r=require,r("./locale/"+e),Ce(t)}catch{H[e]=null}return H[e]}function Ce(e,t){var r;return e&&(q(t)?r=Se(e):r=Nr(e,t),r?tt=r:typeof console<"u"&&console.warn&&console.warn("Locale "+e+" not found. Did you forget to load it?")),tt._abbr}function Nr(e,t){if(t!==null){var r,n=Jn;if(t.abbr=e,H[e]!=null)Fn("defineLocaleOverride","use moment.updateLocale(localeName, config) to change an existing locale. moment.defineLocale(localeName, config) should only be used for creating a new locale See http://momentjs.com/guides/#/warnings/define-locale/ for more info."),n=H[e]._config;else if(t.parentLocale!=null)if(H[t.parentLocale]!=null)n=H[t.parentLocale]._config;else if(r=Et(t.parentLocale),r!=null)n=r._config;else return Ze[t.parentLocale]||(Ze[t.parentLocale]=[]),Ze[t.parentLocale].push({name:e,config:t}),null;return H[e]=new wr(cr(n,t)),Ze[e]&&Ze[e].forEach(function(i){Nr(i.name,i.config)}),Ce(e),H[e]}else return delete H[e],null}function fa(e,t){if(t!=null){var r,n,i=Jn;H[e]!=null&&H[e].parentLocale!=null?H[e].set(cr(H[e]._config,t)):(n=Et(e),n!=null&&(i=n._config),t=cr(i,t),n==null&&(t.abbr=e),r=new wr(t),r.parentLocale=H[e],H[e]=r),Ce(e)}else H[e]!=null&&(H[e].parentLocale!=null?(H[e]=H[e].parentLocale,e===Ce()&&Ce(e)):H[e]!=null&&delete H[e]);return H[e]}function Se(e){var t;if(e&&e._locale&&e._locale._abbr&&(e=e._locale._abbr),!e)return tt;if(!te(e)){if(t=Et(e),t)return t;e=[e]}return ua(e)}function ma(){return ur(H)}function Or(e){var t,r=e._a;return r&&R(e).overflow===-2&&(t=r[ye]<0||r[ye]>11?ye:r[oe]<1||r[oe]>Dr(r[j],r[ye])?oe:r[V]<0||r[V]>24||r[V]===24&&(r[ee]!==0||r[ve]!==0||r[Ne]!==0)?V:r[ee]<0||r[ee]>59?ee:r[ve]<0||r[ve]>59?ve:r[Ne]<0||r[Ne]>999?Ne:-1,R(e)._overflowDayOfYear&&(t<j||t>oe)&&(t=oe),R(e)._overflowWeeks&&t===-1&&(t=vs),R(e)._overflowWeekday&&t===-1&&(t=bs),R(e).overflow=t),e}var ga=/^\s*((?:[+-]\d{6}|\d{4})-(?:\d\d-\d\d|W\d\d-\d|W\d\d|\d\d\d|\d\d))(?:(T| )(\d\d(?::\d\d(?::\d\d(?:[.,]\d+)?)?)?)([+-]\d\d(?::?\d\d)?|\s*Z)?)?$/,pa=/^\s*((?:[+-]\d{6}|\d{4})(?:\d\d\d\d|W\d\d\d|W\d\d|\d\d\d|\d\d|))(?:(T| )(\d\d(?:\d\d(?:\d\d(?:[.,]\d+)?)?)?)([+-]\d\d(?::?\d\d)?|\s*Z)?)?$/,ya=/Z|[+-]\d\d(?::?\d\d)?/,ht=[["YYYYYY-MM-DD",/[+-]\d{6}-\d\d-\d\d/],["YYYY-MM-DD",/\d{4}-\d\d-\d\d/],["GGGG-[W]WW-E",/\d{4}-W\d\d-\d/],["GGGG-[W]WW",/\d{4}-W\d\d/,!1],["YYYY-DDD",/\d{4}-\d{3}/],["YYYY-MM",/\d{4}-\d\d/,!1],["YYYYYYMMDD",/[+-]\d{10}/],["YYYYMMDD",/\d{8}/],["GGGG[W]WWE",/\d{4}W\d{3}/],["GGGG[W]WW",/\d{4}W\d{2}/,!1],["YYYYDDD",/\d{7}/],["YYYYMM",/\d{6}/,!1],["YYYY",/\d{4}/,!1]],or=[["HH:mm:ss.SSSS",/\d\d:\d\d:\d\d\.\d+/],["HH:mm:ss,SSSS",/\d\d:\d\d:\d\d,\d+/],["HH:mm:ss",/\d\d:\d\d:\d\d/],["HH:mm",/\d\d:\d\d/],["HHmmss.SSSS",/\d\d\d\d\d\d\.\d+/],["HHmmss,SSSS",/\d\d\d\d\d\d,\d+/],["HHmmss",/\d\d\d\d\d\d/],["HHmm",/\d\d\d\d/],["HH",/\d\d/]],va=/^\/?Date\((-?\d+)/i,ba=/^(?:(Mon|Tue|Wed|Thu|Fri|Sat|Sun),?\s)?(\d{1,2})\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s(\d{2,4})\s(\d\d):(\d\d)(?::(\d\d))?\s(?:(UT|GMT|[ECMP][SD]T)|([Zz])|([+-]\d{4}))$/,wa={UT:0,GMT:0,EDT:-240,EST:-300,CDT:-300,CST:-360,MDT:-360,MST:-420,PDT:-420,PST:-480};function Kn(e){var t,r,n=e._i,i=ga.exec(n)||pa.exec(n),s,a,l,o,d=ht.length,c=or.length;if(i){for(R(e).iso=!0,t=0,r=d;t<r;t++)if(ht[t][1].exec(i[1])){a=ht[t][0],s=ht[t][2]!==!1;break}if(a==null){e._isValid=!1;return}if(i[3]){for(t=0,r=c;t<r;t++)if(or[t][1].exec(i[3])){l=(i[2]||" ")+or[t][0];break}if(l==null){e._isValid=!1;return}}if(!s&&l!=null){e._isValid=!1;return}if(i[4])if(ya.exec(i[4]))o="Z";else{e._isValid=!1;return}e._f=a+(l||"")+(o||""),Fr(e)}else e._isValid=!1}function _a(e,t,r,n,i,s){var a=[Sa(e),Un.indexOf(t),parseInt(r,10),parseInt(n,10),parseInt(i,10)];return s&&a.push(parseInt(s,10)),a}function Sa(e){var t=parseInt(e,10);return t<=49?2e3+t:t<=999?1900+t:t}function Ma(e){return e.replace(/\([^()]*\)|[\n\t]/g," ").replace(/(\s\s+)/g," ").replace(/^\s\s*/,"").replace(/\s\s*$/,"")}function Ta(e,t,r){if(e){var n=qn.indexOf(e),i=new Date(t[0],t[1],t[2]).getDay();if(n!==i)return R(r).weekdayMismatch=!0,r._isValid=!1,!1}return!0}function xa(e,t,r){if(e)return wa[e];if(t)return 0;var n=parseInt(r,10),i=n%100,s=(n-i)/100;return s*60+i}function Qn(e){var t=ba.exec(Ma(e._i)),r;if(t){if(r=_a(t[4],t[3],t[2],t[5],t[6],t[7]),!Ta(t[1],r,e))return;e._a=r,e._tzm=xa(t[8],t[9],t[10]),e._d=Xe.apply(null,e._a),e._d.setUTCMinutes(e._d.getUTCMinutes()-e._tzm),R(e).rfc2822=!0}else e._isValid=!1}function Da(e){var t=va.exec(e._i);if(t!==null){e._d=new Date(+t[1]);return}if(Kn(e),e._isValid===!1)delete e._isValid;else return;if(Qn(e),e._isValid===!1)delete e._isValid;else return;e._strict?e._isValid=!1:y.createFromInputFallback(e)}y.createFromInputFallback=Q("value provided is not in a recognized RFC2822 or ISO format. moment construction falls back to js Date(), which is not reliable across all browsers and versions. Non RFC2822/ISO date formats are discouraged. Please refer to http://momentjs.com/guides/#/warnings/js-date/ for more info.",function(e){e._d=new Date(e._i+(e._useUTC?" UTC":""))});function Ie(e,t,r){return e??t??r}function Ca(e){var t=new Date(y.now());return e._useUTC?[t.getUTCFullYear(),t.getUTCMonth(),t.getUTCDate()]:[t.getFullYear(),t.getMonth(),t.getDate()]}function Ar(e){var t,r,n=[],i,s,a;if(!e._d){for(i=Ca(e),e._w&&e._a[oe]==null&&e._a[ye]==null&&Ea(e),e._dayOfYear!=null&&(a=Ie(e._a[j],i[j]),(e._dayOfYear>Ke(a)||e._dayOfYear===0)&&(R(e)._overflowDayOfYear=!0),r=Xe(a,0,e._dayOfYear),e._a[ye]=r.getUTCMonth(),e._a[oe]=r.getUTCDate()),t=0;t<3&&e._a[t]==null;++t)e._a[t]=n[t]=i[t];for(;t<7;t++)e._a[t]=n[t]=e._a[t]==null?t===2?1:0:e._a[t];e._a[V]===24&&e._a[ee]===0&&e._a[ve]===0&&e._a[Ne]===0&&(e._nextDay=!0,e._a[V]=0),e._d=(e._useUTC?Xe:Rs).apply(null,n),s=e._useUTC?e._d.getUTCDay():e._d.getDay(),e._tzm!=null&&e._d.setUTCMinutes(e._d.getUTCMinutes()-e._tzm),e._nextDay&&(e._a[V]=24),e._w&&typeof e._w.d<"u"&&e._w.d!==s&&(R(e).weekdayMismatch=!0)}}function Ea(e){var t,r,n,i,s,a,l,o,d;t=e._w,t.GG!=null||t.W!=null||t.E!=null?(s=1,a=4,r=Ie(t.GG,e._a[j],et(U(),1,4).year),n=Ie(t.W,1),i=Ie(t.E,1),(i<1||i>7)&&(o=!0)):(s=e._locale._week.dow,a=e._locale._week.doy,d=et(U(),s,a),r=Ie(t.gg,e._a[j],d.year),n=Ie(t.w,d.week),t.d!=null?(i=t.d,(i<0||i>6)&&(o=!0)):t.e!=null?(i=t.e+s,(t.e<0||t.e>6)&&(o=!0)):i=s),n<1||n>we(r,s,a)?R(e)._overflowWeeks=!0:o!=null?R(e)._overflowWeekday=!0:(l=jn(r,n,i,s,a),e._a[j]=l.year,e._dayOfYear=l.dayOfYear)}y.ISO_8601=function(){};y.RFC_2822=function(){};function Fr(e){if(e._f===y.ISO_8601){Kn(e);return}if(e._f===y.RFC_2822){Qn(e);return}e._a=[],R(e).empty=!0;var t=""+e._i,r,n,i,s,a,l=t.length,o=0,d,c;for(i=Rn(e._f,e._locale).match(_r)||[],c=i.length,r=0;r<c;r++)s=i[r],n=(t.match(gs(s,e))||[])[0],n&&(a=t.substr(0,t.indexOf(n)),a.length>0&&R(e).unusedInput.push(a),t=t.slice(t.indexOf(n)+n.length),o+=n.length),Be[s]?(n?R(e).empty=!1:R(e).unusedTokens.push(s),ys(s,n,e)):e._strict&&!n&&R(e).unusedTokens.push(s);R(e).charsLeftOver=l-o,t.length>0&&R(e).unusedInput.push(t),e._a[V]<=12&&R(e).bigHour===!0&&e._a[V]>0&&(R(e).bigHour=void 0),R(e).parsedDateParts=e._a.slice(0),R(e).meridiem=e._meridiem,e._a[V]=ka(e._locale,e._a[V],e._meridiem),d=R(e).era,d!==null&&(e._a[j]=e._locale.erasConvertYear(d,e._a[j])),Ar(e),Or(e)}function ka(e,t,r){var n;return r==null?t:e.meridiemHour!=null?e.meridiemHour(t,r):(e.isPM!=null&&(n=e.isPM(r),n&&t<12&&(t+=12),!n&&t===12&&(t=0)),t)}function Na(e){var t,r,n,i,s,a,l=!1,o=e._f.length;if(o===0){R(e).invalidFormat=!0,e._d=new Date(NaN);return}for(i=0;i<o;i++)s=0,a=!1,t=br({},e),e._useUTC!=null&&(t._useUTC=e._useUTC),t._f=e._f[i],Fr(t),vr(t)&&(a=!0),s+=R(t).charsLeftOver,s+=R(t).unusedTokens.length*10,R(t).score=s,l?s<n&&(n=s,r=t):(n==null||s<n||a)&&(n=s,r=t,a&&(l=!0));xe(e,r||t)}function Oa(e){if(!e._d){var t=Sr(e._i),r=t.day===void 0?t.date:t.day;e._a=On([t.year,t.month,r,t.hour,t.minute,t.second,t.millisecond],function(n){return n&&parseInt(n,10)}),Ar(e)}}function Aa(e){var t=new nt(Or(Xn(e)));return t._nextDay&&(t.add(1,"d"),t._nextDay=void 0),t}function Xn(e){var t=e._i,r=e._f;return e._locale=e._locale||Se(e._l),t===null||r===void 0&&t===""?_t({nullInput:!0}):(typeof t=="string"&&(e._i=t=e._locale.preparse(t)),re(t)?new nt(Or(t)):(rt(t)?e._d=t:te(r)?Na(e):r?Fr(e):Fa(e),vr(e)||(e._d=null),e))}function Fa(e){var t=e._i;q(t)?e._d=new Date(y.now()):rt(t)?e._d=new Date(t.valueOf()):typeof t=="string"?Da(e):te(t)?(e._a=On(t.slice(0),function(r){return parseInt(r,10)}),Ar(e)):Oe(t)?Oa(e):_e(t)?e._d=new Date(t):y.createFromInputFallback(e)}function ei(e,t,r,n,i){var s={};return(t===!0||t===!1)&&(n=t,t=void 0),(r===!0||r===!1)&&(n=r,r=void 0),(Oe(e)&&yr(e)||te(e)&&e.length===0)&&(e=void 0),s._isAMomentObject=!0,s._useUTC=s._isUTC=i,s._l=r,s._i=e,s._f=t,s._strict=n,Aa(s)}function U(e,t,r,n){return ei(e,t,r,n,!1)}var Ra=Q("moment().min is deprecated, use moment.max instead. http://momentjs.com/guides/#/warnings/min-max/",function(){var e=U.apply(null,arguments);return this.isValid()&&e.isValid()?e<this?this:e:_t()}),Pa=Q("moment().max is deprecated, use moment.min instead. http://momentjs.com/guides/#/warnings/min-max/",function(){var e=U.apply(null,arguments);return this.isValid()&&e.isValid()?e>this?this:e:_t()});function ti(e,t){var r,n;if(t.length===1&&te(t[0])&&(t=t[0]),!t.length)return U();for(r=t[0],n=1;n<t.length;++n)(!t[n].isValid()||t[n][e](r))&&(r=t[n]);return r}function $a(){var e=[].slice.call(arguments,0);return ti("isBefore",e)}function La(){var e=[].slice.call(arguments,0);return ti("isAfter",e)}var Ia=function(){return Date.now?Date.now():+new Date},Je=["year","quarter","month","week","day","hour","minute","second","millisecond"];function Ya(e){var t,r=!1,n,i=Je.length;for(t in e)if(L(e,t)&&!(W.call(Je,t)!==-1&&(e[t]==null||!isNaN(e[t]))))return!1;for(n=0;n<i;++n)if(e[Je[n]]){if(r)return!1;parseFloat(e[Je[n]])!==P(e[Je[n]])&&(r=!0)}return!0}function Ba(){return this._isValid}function Ua(){return ne(NaN)}function kt(e){var t=Sr(e),r=t.year||0,n=t.quarter||0,i=t.month||0,s=t.week||t.isoWeek||0,a=t.day||0,l=t.hour||0,o=t.minute||0,d=t.second||0,c=t.millisecond||0;this._isValid=Ya(t),this._milliseconds=+c+d*1e3+o*6e4+l*1e3*60*60,this._days=+a+s*7,this._months=+i+n*3+r*12,this._data={},this._locale=Se(),this._bubble()}function mt(e){return e instanceof kt}function fr(e){return e<0?Math.round(-1*e)*-1:Math.round(e)}function za(e,t,r){var n=Math.min(e.length,t.length),i=Math.abs(e.length-t.length),s=0,a;for(a=0;a<n;a++)P(e[a])!==P(t[a])&&s++;return s+i}function ri(e,t){x(e,0,0,function(){var r=this.utcOffset(),n="+";return r<0&&(r=-r,n="-"),n+le(~~(r/60),2)+t+le(~~r%60,2)})}ri("Z",":");ri("ZZ","");b("Z",Dt);b("ZZ",Dt);Y(["Z","ZZ"],function(e,t,r){r._useUTC=!0,r._tzm=Rr(Dt,e)});var Ha=/([\+\-]|\d\d)/gi;function Rr(e,t){var r=(t||"").match(e),n,i,s;return r===null?null:(n=r[r.length-1]||[],i=(n+"").match(Ha)||["-",0,0],s=+(i[1]*60)+P(i[2]),s===0?0:i[0]==="+"?s:-s)}function Pr(e,t){var r,n;return t._isUTC?(r=t.clone(),n=(re(e)||rt(e)?e.valueOf():U(e).valueOf())-r.valueOf(),r._d.setTime(r._d.valueOf()+n),y.updateOffset(r,!1),r):U(e).local()}function mr(e){return-Math.round(e._d.getTimezoneOffset())}y.updateOffset=function(){};function Wa(e,t,r){var n=this._offset||0,i;if(!this.isValid())return e!=null?this:NaN;if(e!=null){if(typeof e=="string"){if(e=Rr(Dt,e),e===null)return this}else Math.abs(e)<16&&!r&&(e=e*60);return!this._isUTC&&t&&(i=mr(this)),this._offset=e,this._isUTC=!0,i!=null&&this.add(i,"m"),n!==e&&(!t||this._changeInProgress?si(this,ne(e-n,"m"),1,!1):this._changeInProgress||(this._changeInProgress=!0,y.updateOffset(this,!0),this._changeInProgress=null)),this}else return this._isUTC?n:mr(this)}function Va(e,t){return e!=null?(typeof e!="string"&&(e=-e),this.utcOffset(e,t),this):-this.utcOffset()}function ja(e){return this.utcOffset(0,e)}function qa(e){return this._isUTC&&(this.utcOffset(0,e),this._isUTC=!1,e&&this.subtract(mr(this),"m")),this}function Ga(){if(this._tzm!=null)this.utcOffset(this._tzm,!1,!0);else if(typeof this._i=="string"){var e=Rr(fs,this._i);e!=null?this.utcOffset(e):this.utcOffset(0,!0)}return this}function Za(e){return this.isValid()?(e=e?U(e).utcOffset():0,(this.utcOffset()-e)%60===0):!1}function Ja(){return this.utcOffset()>this.clone().month(0).utcOffset()||this.utcOffset()>this.clone().month(5).utcOffset()}function Ka(){if(!q(this._isDSTShifted))return this._isDSTShifted;var e={},t;return br(e,this),e=Xn(e),e._a?(t=e._isUTC?de(e._a):U(e._a),this._isDSTShifted=this.isValid()&&za(e._a,t.toArray())>0):this._isDSTShifted=!1,this._isDSTShifted}function Qa(){return this.isValid()?!this._isUTC:!1}function Xa(){return this.isValid()?this._isUTC:!1}function ni(){return this.isValid()?this._isUTC&&this._offset===0:!1}var eo=/^(-|\+)?(?:(\d*)[. ])?(\d+):(\d+)(?::(\d+)(\.\d*)?)?$/,to=/^(-|\+)?P(?:([-+]?[0-9,.]*)Y)?(?:([-+]?[0-9,.]*)M)?(?:([-+]?[0-9,.]*)W)?(?:([-+]?[0-9,.]*)D)?(?:T(?:([-+]?[0-9,.]*)H)?(?:([-+]?[0-9,.]*)M)?(?:([-+]?[0-9,.]*)S)?)?$/;function ne(e,t){var r=e,n=null,i,s,a;return mt(e)?r={ms:e._milliseconds,d:e._days,M:e._months}:_e(e)||!isNaN(+e)?(r={},t?r[t]=+e:r.milliseconds=+e):(n=eo.exec(e))?(i=n[1]==="-"?-1:1,r={y:0,d:P(n[oe])*i,h:P(n[V])*i,m:P(n[ee])*i,s:P(n[ve])*i,ms:P(fr(n[Ne]*1e3))*i}):(n=to.exec(e))?(i=n[1]==="-"?-1:1,r={y:ke(n[2],i),M:ke(n[3],i),w:ke(n[4],i),d:ke(n[5],i),h:ke(n[6],i),m:ke(n[7],i),s:ke(n[8],i)}):r==null?r={}:typeof r=="object"&&("from"in r||"to"in r)&&(a=ro(U(r.from),U(r.to)),r={},r.ms=a.milliseconds,r.M=a.months),s=new kt(r),mt(e)&&L(e,"_locale")&&(s._locale=e._locale),mt(e)&&L(e,"_isValid")&&(s._isValid=e._isValid),s}ne.fn=kt.prototype;ne.invalid=Ua;function ke(e,t){var r=e&&parseFloat(e.replace(",","."));return(isNaN(r)?0:r)*t}function Mn(e,t){var r={};return r.months=t.month()-e.month()+(t.year()-e.year())*12,e.clone().add(r.months,"M").isAfter(t)&&--r.months,r.milliseconds=+t-+e.clone().add(r.months,"M"),r}function ro(e,t){var r;return e.isValid()&&t.isValid()?(t=Pr(t,e),e.isBefore(t)?r=Mn(e,t):(r=Mn(t,e),r.milliseconds=-r.milliseconds,r.months=-r.months),r):{milliseconds:0,months:0}}function ii(e,t){return function(r,n){var i,s;return n!==null&&!isNaN(+n)&&(Fn(t,"moment()."+t+"(period, number) is deprecated. Please use moment()."+t+"(number, period). See http://momentjs.com/guides/#/warnings/add-inverted-param/ for more info."),s=r,r=n,n=s),i=ne(r,n),si(this,i,e),this}}function si(e,t,r,n){var i=t._milliseconds,s=fr(t._days),a=fr(t._months);e.isValid()&&(n=n??!0,a&&Hn(e,Qe(e,"Month")+a*r),s&&Bn(e,"Date",Qe(e,"Date")+s*r),i&&e._d.setTime(e._d.valueOf()+i*r),n&&y.updateOffset(e,s||a))}var no=ii(1,"add"),io=ii(-1,"subtract");function ai(e){return typeof e=="string"||e instanceof String}function so(e){return re(e)||rt(e)||ai(e)||_e(e)||oo(e)||ao(e)||e===null||e===void 0}function ao(e){var t=Oe(e)&&!yr(e),r=!1,n=["years","year","y","months","month","M","days","day","d","dates","date","D","hours","hour","h","minutes","minute","m","seconds","second","s","milliseconds","millisecond","ms"],i,s,a=n.length;for(i=0;i<a;i+=1)s=n[i],r=r||L(e,s);return t&&r}function oo(e){var t=te(e),r=!1;return t&&(r=e.filter(function(n){return!_e(n)&&ai(e)}).length===0),t&&r}function lo(e){var t=Oe(e)&&!yr(e),r=!1,n=["sameDay","nextDay","lastDay","nextWeek","lastWeek","sameElse"],i,s;for(i=0;i<n.length;i+=1)s=n[i],r=r||L(e,s);return t&&r}function co(e,t){var r=e.diff(t,"days",!0);return r<-6?"sameElse":r<-1?"lastWeek":r<0?"lastDay":r<1?"sameDay":r<2?"nextDay":r<7?"nextWeek":"sameElse"}function uo(e,t){arguments.length===1&&(arguments[0]?so(arguments[0])?(e=arguments[0],t=void 0):lo(arguments[0])&&(t=arguments[0],e=void 0):(e=void 0,t=void 0));var r=e||U(),n=Pr(r,this).startOf("day"),i=y.calendarFormat(this,n)||"sameElse",s=t&&(ce(t[i])?t[i].call(this,r):t[i]);return this.format(s||this.localeData().calendar(i,this,U(r)))}function ho(){return new nt(this)}function fo(e,t){var r=re(e)?e:U(e);return this.isValid()&&r.isValid()?(t=X(t)||"millisecond",t==="millisecond"?this.valueOf()>r.valueOf():r.valueOf()<this.clone().startOf(t).valueOf()):!1}function mo(e,t){var r=re(e)?e:U(e);return this.isValid()&&r.isValid()?(t=X(t)||"millisecond",t==="millisecond"?this.valueOf()<r.valueOf():this.clone().endOf(t).valueOf()<r.valueOf()):!1}function go(e,t,r,n){var i=re(e)?e:U(e),s=re(t)?t:U(t);return this.isValid()&&i.isValid()&&s.isValid()?(n=n||"()",(n[0]==="("?this.isAfter(i,r):!this.isBefore(i,r))&&(n[1]===")"?this.isBefore(s,r):!this.isAfter(s,r))):!1}function po(e,t){var r=re(e)?e:U(e),n;return this.isValid()&&r.isValid()?(t=X(t)||"millisecond",t==="millisecond"?this.valueOf()===r.valueOf():(n=r.valueOf(),this.clone().startOf(t).valueOf()<=n&&n<=this.clone().endOf(t).valueOf())):!1}function yo(e,t){return this.isSame(e,t)||this.isAfter(e,t)}function vo(e,t){return this.isSame(e,t)||this.isBefore(e,t)}function bo(e,t,r){var n,i,s;if(!this.isValid())return NaN;if(n=Pr(e,this),!n.isValid())return NaN;switch(i=(n.utcOffset()-this.utcOffset())*6e4,t=X(t),t){case"year":s=gt(this,n)/12;break;case"month":s=gt(this,n);break;case"quarter":s=gt(this,n)/3;break;case"second":s=(this-n)/1e3;break;case"minute":s=(this-n)/6e4;break;case"hour":s=(this-n)/36e5;break;case"day":s=(this-n-i)/864e5;break;case"week":s=(this-n-i)/6048e5;break;default:s=this-n}return r?s:K(s)}function gt(e,t){if(e.date()<t.date())return-gt(t,e);var r=(t.year()-e.year())*12+(t.month()-e.month()),n=e.clone().add(r,"months"),i,s;return t-n<0?(i=e.clone().add(r-1,"months"),s=(t-n)/(n-i)):(i=e.clone().add(r+1,"months"),s=(t-n)/(i-n)),-(r+s)||0}y.defaultFormat="YYYY-MM-DDTHH:mm:ssZ";y.defaultFormatUtc="YYYY-MM-DDTHH:mm:ss[Z]";function wo(){return this.clone().locale("en").format("ddd MMM DD YYYY HH:mm:ss [GMT]ZZ")}function _o(e){if(!this.isValid())return null;var t=e!==!0,r=t?this.clone().utc():this;return r.year()<0||r.year()>9999?ft(r,t?"YYYYYY-MM-DD[T]HH:mm:ss.SSS[Z]":"YYYYYY-MM-DD[T]HH:mm:ss.SSSZ"):ce(Date.prototype.toISOString)?t?this.toDate().toISOString():new Date(this.valueOf()+this.utcOffset()*60*1e3).toISOString().replace("Z",ft(r,"Z")):ft(r,t?"YYYY-MM-DD[T]HH:mm:ss.SSS[Z]":"YYYY-MM-DD[T]HH:mm:ss.SSSZ")}function So(){if(!this.isValid())return"moment.invalid(/* "+this._i+" */)";var e="moment",t="",r,n,i,s;return this.isLocal()||(e=this.utcOffset()===0?"moment.utc":"moment.parseZone",t="Z"),r="["+e+'("]',n=0<=this.year()&&this.year()<=9999?"YYYY":"YYYYYY",i="-MM-DD[T]HH:mm:ss.SSS",s=t+'[")]',this.format(r+n+i+s)}function Mo(e){e||(e=this.isUtc()?y.defaultFormatUtc:y.defaultFormat);var t=ft(this,e);return this.localeData().postformat(t)}function To(e,t){return this.isValid()&&(re(e)&&e.isValid()||U(e).isValid())?ne({to:this,from:e}).locale(this.locale()).humanize(!t):this.localeData().invalidDate()}function xo(e){return this.from(U(),e)}function Do(e,t){return this.isValid()&&(re(e)&&e.isValid()||U(e).isValid())?ne({from:this,to:e}).locale(this.locale()).humanize(!t):this.localeData().invalidDate()}function Co(e){return this.to(U(),e)}function oi(e){var t;return e===void 0?this._locale._abbr:(t=Se(e),t!=null&&(this._locale=t),this)}var li=Q("moment().lang() is deprecated. Instead, use moment().localeData() to get the language configuration. Use moment().locale() to change languages.",function(e){return e===void 0?this.localeData():this.locale(e)});function di(){return this._locale}var vt=1e3,Ue=60*vt,bt=60*Ue,ci=(365*400+97)*24*bt;function ze(e,t){return(e%t+t)%t}function ui(e,t,r){return e<100&&e>=0?new Date(e+400,t,r)-ci:new Date(e,t,r).valueOf()}function hi(e,t,r){return e<100&&e>=0?Date.UTC(e+400,t,r)-ci:Date.UTC(e,t,r)}function Eo(e){var t,r;if(e=X(e),e===void 0||e==="millisecond"||!this.isValid())return this;switch(r=this._isUTC?hi:ui,e){case"year":t=r(this.year(),0,1);break;case"quarter":t=r(this.year(),this.month()-this.month()%3,1);break;case"month":t=r(this.year(),this.month(),1);break;case"week":t=r(this.year(),this.month(),this.date()-this.weekday());break;case"isoWeek":t=r(this.year(),this.month(),this.date()-(this.isoWeekday()-1));break;case"day":case"date":t=r(this.year(),this.month(),this.date());break;case"hour":t=this._d.valueOf(),t-=ze(t+(this._isUTC?0:this.utcOffset()*Ue),bt);break;case"minute":t=this._d.valueOf(),t-=ze(t,Ue);break;case"second":t=this._d.valueOf(),t-=ze(t,vt);break}return this._d.setTime(t),y.updateOffset(this,!0),this}function ko(e){var t,r;if(e=X(e),e===void 0||e==="millisecond"||!this.isValid())return this;switch(r=this._isUTC?hi:ui,e){case"year":t=r(this.year()+1,0,1)-1;break;case"quarter":t=r(this.year(),this.month()-this.month()%3+3,1)-1;break;case"month":t=r(this.year(),this.month()+1,1)-1;break;case"week":t=r(this.year(),this.month(),this.date()-this.weekday()+7)-1;break;case"isoWeek":t=r(this.year(),this.month(),this.date()-(this.isoWeekday()-1)+7)-1;break;case"day":case"date":t=r(this.year(),this.month(),this.date()+1)-1;break;case"hour":t=this._d.valueOf(),t+=bt-ze(t+(this._isUTC?0:this.utcOffset()*Ue),bt)-1;break;case"minute":t=this._d.valueOf(),t+=Ue-ze(t,Ue)-1;break;case"second":t=this._d.valueOf(),t+=vt-ze(t,vt)-1;break}return this._d.setTime(t),y.updateOffset(this,!0),this}function No(){return this._d.valueOf()-(this._offset||0)*6e4}function Oo(){return Math.floor(this.valueOf()/1e3)}function Ao(){return new Date(this.valueOf())}function Fo(){var e=this;return[e.year(),e.month(),e.date(),e.hour(),e.minute(),e.second(),e.millisecond()]}function Ro(){var e=this;return{years:e.year(),months:e.month(),date:e.date(),hours:e.hours(),minutes:e.minutes(),seconds:e.seconds(),milliseconds:e.milliseconds()}}function Po(){return this.isValid()?this.toISOString():null}function $o(){return vr(this)}function Lo(){return xe({},R(this))}function Io(){return R(this).overflow}function Yo(){return{input:this._i,format:this._f,locale:this._locale,isUTC:this._isUTC,strict:this._strict}}x("N",0,0,"eraAbbr");x("NN",0,0,"eraAbbr");x("NNN",0,0,"eraAbbr");x("NNNN",0,0,"eraName");x("NNNNN",0,0,"eraNarrow");x("y",["y",1],"yo","eraYear");x("y",["yy",2],0,"eraYear");x("y",["yyy",3],0,"eraYear");x("y",["yyyy",4],0,"eraYear");b("N",$r);b("NN",$r);b("NNN",$r);b("NNNN",Jo);b("NNNNN",Ko);Y(["N","NN","NNN","NNNN","NNNNN"],function(e,t,r,n){var i=r._locale.erasParse(e,n,r._strict);i?R(r).era=i:R(r).invalidEra=e});b("y",He);b("yy",He);b("yyy",He);b("yyyy",He);b("yo",Qo);Y(["y","yy","yyy","yyyy"],j);Y(["yo"],function(e,t,r,n){var i;r._locale._eraYearOrdinalRegex&&(i=e.match(r._locale._eraYearOrdinalRegex)),r._locale.eraYearOrdinalParse?t[j]=r._locale.eraYearOrdinalParse(e,i):t[j]=parseInt(e,10)});function Bo(e,t){var r,n,i,s=this._eras||Se("en")._eras;for(r=0,n=s.length;r<n;++r)switch(typeof s[r].since==="string"&&(i=y(s[r].since).startOf("day"),s[r].since=i.valueOf()),typeof s[r].until){case"undefined":s[r].until=1/0;break;case"string":i=y(s[r].until).startOf("day").valueOf(),s[r].until=i.valueOf();break}return s}function Uo(e,t,r){var n,i,s=this.eras(),a,l,o;for(e=e.toUpperCase(),n=0,i=s.length;n<i;++n)if(a=s[n].name.toUpperCase(),l=s[n].abbr.toUpperCase(),o=s[n].narrow.toUpperCase(),r)switch(t){case"N":case"NN":case"NNN":if(l===e)return s[n];break;case"NNNN":if(a===e)return s[n];break;case"NNNNN":if(o===e)return s[n];break}else if([a,l,o].indexOf(e)>=0)return s[n]}function zo(e,t){var r=e.since<=e.until?1:-1;return t===void 0?y(e.since).year():y(e.since).year()+(t-e.offset)*r}function Ho(){var e,t,r,n=this.localeData().eras();for(e=0,t=n.length;e<t;++e)if(r=this.clone().startOf("day").valueOf(),n[e].since<=r&&r<=n[e].until||n[e].until<=r&&r<=n[e].since)return n[e].name;return""}function Wo(){var e,t,r,n=this.localeData().eras();for(e=0,t=n.length;e<t;++e)if(r=this.clone().startOf("day").valueOf(),n[e].since<=r&&r<=n[e].until||n[e].until<=r&&r<=n[e].since)return n[e].narrow;return""}function Vo(){var e,t,r,n=this.localeData().eras();for(e=0,t=n.length;e<t;++e)if(r=this.clone().startOf("day").valueOf(),n[e].since<=r&&r<=n[e].until||n[e].until<=r&&r<=n[e].since)return n[e].abbr;return""}function jo(){var e,t,r,n,i=this.localeData().eras();for(e=0,t=i.length;e<t;++e)if(r=i[e].since<=i[e].until?1:-1,n=this.clone().startOf("day").valueOf(),i[e].since<=n&&n<=i[e].until||i[e].until<=n&&n<=i[e].since)return(this.year()-y(i[e].since).year())*r+i[e].offset;return this.year()}function qo(e){return L(this,"_erasNameRegex")||Lr.call(this),e?this._erasNameRegex:this._erasRegex}function Go(e){return L(this,"_erasAbbrRegex")||Lr.call(this),e?this._erasAbbrRegex:this._erasRegex}function Zo(e){return L(this,"_erasNarrowRegex")||Lr.call(this),e?this._erasNarrowRegex:this._erasRegex}function $r(e,t){return t.erasAbbrRegex(e)}function Jo(e,t){return t.erasNameRegex(e)}function Ko(e,t){return t.erasNarrowRegex(e)}function Qo(e,t){return t._eraYearOrdinalRegex||He}function Lr(){var e=[],t=[],r=[],n=[],i,s,a,l,o,d=this.eras();for(i=0,s=d.length;i<s;++i)a=be(d[i].name),l=be(d[i].abbr),o=be(d[i].narrow),t.push(a),e.push(l),r.push(o),n.push(a),n.push(l),n.push(o);this._erasRegex=new RegExp("^("+n.join("|")+")","i"),this._erasNameRegex=new RegExp("^("+t.join("|")+")","i"),this._erasAbbrRegex=new RegExp("^("+e.join("|")+")","i"),this._erasNarrowRegex=new RegExp("^("+r.join("|")+")","i")}x(0,["gg",2],0,function(){return this.weekYear()%100});x(0,["GG",2],0,function(){return this.isoWeekYear()%100});function Nt(e,t){x(0,[e,e.length],0,t)}Nt("gggg","weekYear");Nt("ggggg","weekYear");Nt("GGGG","isoWeekYear");Nt("GGGGG","isoWeekYear");b("G",xt);b("g",xt);b("GG",z,G);b("gg",z,G);b("GGGG",Tr,Mr);b("gggg",Tr,Mr);b("GGGGG",Tt,St);b("ggggg",Tt,St);st(["gggg","ggggg","GGGG","GGGGG"],function(e,t,r,n){t[n.substr(0,2)]=P(e)});st(["gg","GG"],function(e,t,r,n){t[n]=y.parseTwoDigitYear(e)});function Xo(e){return fi.call(this,e,this.week(),this.weekday()+this.localeData()._week.dow,this.localeData()._week.dow,this.localeData()._week.doy)}function el(e){return fi.call(this,e,this.isoWeek(),this.isoWeekday(),1,4)}function tl(){return we(this.year(),1,4)}function rl(){return we(this.isoWeekYear(),1,4)}function nl(){var e=this.localeData()._week;return we(this.year(),e.dow,e.doy)}function il(){var e=this.localeData()._week;return we(this.weekYear(),e.dow,e.doy)}function fi(e,t,r,n,i){var s;return e==null?et(this,n,i).year:(s=we(e,n,i),t>s&&(t=s),sl.call(this,e,t,r,n,i))}function sl(e,t,r,n,i){var s=jn(e,t,r,n,i),a=Xe(s.year,0,s.dayOfYear);return this.year(a.getUTCFullYear()),this.month(a.getUTCMonth()),this.date(a.getUTCDate()),this}x("Q",0,"Qo","quarter");b("Q",Pn);Y("Q",function(e,t){t[ye]=(P(e)-1)*3});function al(e){return e==null?Math.ceil((this.month()+1)/3):this.month((e-1)*3+this.month()%3)}x("D",["DD",2],"Do","date");b("D",z,We);b("DD",z,G);b("Do",function(e,t){return e?t._dayOfMonthOrdinalParse||t._ordinalParse:t._dayOfMonthOrdinalParseLenient});Y(["D","DD"],oe);Y("Do",function(e,t){t[oe]=P(e.match(z)[0])});var mi=Ve("Date",!0);x("DDD",["DDDD",3],"DDDo","dayOfYear");b("DDD",Mt);b("DDDD",$n);Y(["DDD","DDDD"],function(e,t,r){r._dayOfYear=P(e)});function ol(e){var t=Math.round((this.clone().startOf("day")-this.clone().startOf("year"))/864e5)+1;return e==null?t:this.add(e-t,"d")}x("m",["mm",2],0,"minute");b("m",z,xr);b("mm",z,G);Y(["m","mm"],ee);var ll=Ve("Minutes",!1);x("s",["ss",2],0,"second");b("s",z,xr);b("ss",z,G);Y(["s","ss"],ve);var dl=Ve("Seconds",!1);x("S",0,0,function(){return~~(this.millisecond()/100)});x(0,["SS",2],0,function(){return~~(this.millisecond()/10)});x(0,["SSS",3],0,"millisecond");x(0,["SSSS",4],0,function(){return this.millisecond()*10});x(0,["SSSSS",5],0,function(){return this.millisecond()*100});x(0,["SSSSSS",6],0,function(){return this.millisecond()*1e3});x(0,["SSSSSSS",7],0,function(){return this.millisecond()*1e4});x(0,["SSSSSSSS",8],0,function(){return this.millisecond()*1e5});x(0,["SSSSSSSSS",9],0,function(){return this.millisecond()*1e6});b("S",Mt,Pn);b("SS",Mt,G);b("SSS",Mt,$n);var De,gi;for(De="SSSS";De.length<=9;De+="S")b(De,He);function cl(e,t){t[Ne]=P(("0."+e)*1e3)}for(De="S";De.length<=9;De+="S")Y(De,cl);gi=Ve("Milliseconds",!1);x("z",0,0,"zoneAbbr");x("zz",0,0,"zoneName");function ul(){return this._isUTC?"UTC":""}function hl(){return this._isUTC?"Coordinated Universal Time":""}var m=nt.prototype;m.add=no;m.calendar=uo;m.clone=ho;m.diff=bo;m.endOf=ko;m.format=Mo;m.from=To;m.fromNow=xo;m.to=Do;m.toNow=Co;m.get=_s;m.invalidAt=Io;m.isAfter=fo;m.isBefore=mo;m.isBetween=go;m.isSame=po;m.isSameOrAfter=yo;m.isSameOrBefore=vo;m.isValid=$o;m.lang=li;m.locale=oi;m.localeData=di;m.max=Pa;m.min=Ra;m.parsingFlags=Lo;m.set=Ss;m.startOf=Eo;m.subtract=io;m.toArray=Fo;m.toObject=Ro;m.toDate=Ao;m.toISOString=_o;m.inspect=So;typeof Symbol<"u"&&Symbol.for!=null&&(m[Symbol.for("nodejs.util.inspect.custom")]=function(){return"Moment<"+this.format()+">"});m.toJSON=Po;m.toString=wo;m.unix=Oo;m.valueOf=No;m.creationData=Yo;m.eraName=Ho;m.eraNarrow=Wo;m.eraAbbr=Vo;m.eraYear=jo;m.year=Yn;m.isLeapYear=ws;m.weekYear=Xo;m.isoWeekYear=el;m.quarter=m.quarters=al;m.month=Wn;m.daysInMonth=Os;m.week=m.weeks=Ys;m.isoWeek=m.isoWeeks=Bs;m.weeksInYear=nl;m.weeksInWeekYear=il;m.isoWeeksInYear=tl;m.isoWeeksInISOWeekYear=rl;m.date=mi;m.day=m.days=Xs;m.weekday=ea;m.isoWeekday=ta;m.dayOfYear=ol;m.hour=m.hours=la;m.minute=m.minutes=ll;m.second=m.seconds=dl;m.millisecond=m.milliseconds=gi;m.utcOffset=Wa;m.utc=ja;m.local=qa;m.parseZone=Ga;m.hasAlignedHourOffset=Za;m.isDST=Ja;m.isLocal=Qa;m.isUtcOffset=Xa;m.isUtc=ni;m.isUTC=ni;m.zoneAbbr=ul;m.zoneName=hl;m.dates=Q("dates accessor is deprecated. Use date instead.",mi);m.months=Q("months accessor is deprecated. Use month instead",Wn);m.years=Q("years accessor is deprecated. Use year instead",Yn);m.zone=Q("moment().zone is deprecated, use moment().utcOffset instead. http://momentjs.com/guides/#/warnings/zone/",Va);m.isDSTShifted=Q("isDSTShifted is deprecated. See http://momentjs.com/guides/#/warnings/dst-shifted/ for more information",Ka);function fl(e){return U(e*1e3)}function ml(){return U.apply(null,arguments).parseZone()}function pi(e){return e}var I=wr.prototype;I.calendar=Qi;I.longDateFormat=rs;I.invalidDate=is;I.ordinal=os;I.preparse=pi;I.postformat=pi;I.relativeTime=ds;I.pastFuture=cs;I.set=Ji;I.eras=Bo;I.erasParse=Uo;I.erasConvertYear=zo;I.erasAbbrRegex=Go;I.erasNameRegex=qo;I.erasNarrowRegex=Zo;I.months=Cs;I.monthsShort=Es;I.monthsParse=Ns;I.monthsRegex=Fs;I.monthsShortRegex=As;I.week=Ps;I.firstDayOfYear=Is;I.firstDayOfWeek=Ls;I.weekdays=Gs;I.weekdaysMin=Js;I.weekdaysShort=Zs;I.weekdaysParse=Qs;I.weekdaysRegex=ra;I.weekdaysShortRegex=na;I.weekdaysMinRegex=ia;I.isPM=aa;I.meridiem=da;function wt(e,t,r,n){var i=Se(),s=de().set(n,t);return i[r](s,e)}function yi(e,t,r){if(_e(e)&&(t=e,e=void 0),e=e||"",t!=null)return wt(e,t,r,"month");var n,i=[];for(n=0;n<12;n++)i[n]=wt(e,n,r,"month");return i}function Ir(e,t,r,n){typeof e=="boolean"?(_e(t)&&(r=t,t=void 0),t=t||""):(t=e,r=t,e=!1,_e(t)&&(r=t,t=void 0),t=t||"");var i=Se(),s=e?i._week.dow:0,a,l=[];if(r!=null)return wt(t,(r+s)%7,n,"day");for(a=0;a<7;a++)l[a]=wt(t,(a+s)%7,n,"day");return l}function gl(e,t){return yi(e,t,"months")}function pl(e,t){return yi(e,t,"monthsShort")}function yl(e,t,r){return Ir(e,t,r,"weekdays")}function vl(e,t,r){return Ir(e,t,r,"weekdaysShort")}function bl(e,t,r){return Ir(e,t,r,"weekdaysMin")}Ce("en",{eras:[{since:"0001-01-01",until:1/0,offset:1,name:"Anno Domini",narrow:"AD",abbr:"AD"},{since:"0000-12-31",until:-1/0,offset:1,name:"Before Christ",narrow:"BC",abbr:"BC"}],dayOfMonthOrdinalParse:/\d{1,2}(th|st|nd|rd)/,ordinal:function(e){var t=e%10,r=P(e%100/10)===1?"th":t===1?"st":t===2?"nd":t===3?"rd":"th";return e+r}});y.lang=Q("moment.lang is deprecated. Use moment.locale instead.",Ce);y.langData=Q("moment.langData is deprecated. Use moment.localeData instead.",Se);var ge=Math.abs;function wl(){var e=this._data;return this._milliseconds=ge(this._milliseconds),this._days=ge(this._days),this._months=ge(this._months),e.milliseconds=ge(e.milliseconds),e.seconds=ge(e.seconds),e.minutes=ge(e.minutes),e.hours=ge(e.hours),e.months=ge(e.months),e.years=ge(e.years),this}function vi(e,t,r,n){var i=ne(t,r);return e._milliseconds+=n*i._milliseconds,e._days+=n*i._days,e._months+=n*i._months,e._bubble()}function _l(e,t){return vi(this,e,t,1)}function Sl(e,t){return vi(this,e,t,-1)}function Tn(e){return e<0?Math.floor(e):Math.ceil(e)}function Ml(){var e=this._milliseconds,t=this._days,r=this._months,n=this._data,i,s,a,l,o;return e>=0&&t>=0&&r>=0||e<=0&&t<=0&&r<=0||(e+=Tn(gr(r)+t)*864e5,t=0,r=0),n.milliseconds=e%1e3,i=K(e/1e3),n.seconds=i%60,s=K(i/60),n.minutes=s%60,a=K(s/60),n.hours=a%24,t+=K(a/24),o=K(bi(t)),r+=o,t-=Tn(gr(o)),l=K(r/12),r%=12,n.days=t,n.months=r,n.years=l,this}function bi(e){return e*4800/146097}function gr(e){return e*146097/4800}function Tl(e){if(!this.isValid())return NaN;var t,r,n=this._milliseconds;if(e=X(e),e==="month"||e==="quarter"||e==="year")switch(t=this._days+n/864e5,r=this._months+bi(t),e){case"month":return r;case"quarter":return r/3;case"year":return r/12}else switch(t=this._days+Math.round(gr(this._months)),e){case"week":return t/7+n/6048e5;case"day":return t+n/864e5;case"hour":return t*24+n/36e5;case"minute":return t*1440+n/6e4;case"second":return t*86400+n/1e3;case"millisecond":return Math.floor(t*864e5)+n;default:throw new Error("Unknown unit "+e)}}function Me(e){return function(){return this.as(e)}}var wi=Me("ms"),xl=Me("s"),Dl=Me("m"),Cl=Me("h"),El=Me("d"),kl=Me("w"),Nl=Me("M"),Ol=Me("Q"),Al=Me("y"),Fl=wi;function Rl(){return ne(this)}function Pl(e){return e=X(e),this.isValid()?this[e+"s"]():NaN}function Re(e){return function(){return this.isValid()?this._data[e]:NaN}}var $l=Re("milliseconds"),Ll=Re("seconds"),Il=Re("minutes"),Yl=Re("hours"),Bl=Re("days"),Ul=Re("months"),zl=Re("years");function Hl(){return K(this.days()/7)}var pe=Math.round,Ye={ss:44,s:45,m:45,h:22,d:26,w:null,M:11};function Wl(e,t,r,n,i){return i.relativeTime(t||1,!!r,e,n)}function Vl(e,t,r,n){var i=ne(e).abs(),s=pe(i.as("s")),a=pe(i.as("m")),l=pe(i.as("h")),o=pe(i.as("d")),d=pe(i.as("M")),c=pe(i.as("w")),u=pe(i.as("y")),h=s<=r.ss&&["s",s]||s<r.s&&["ss",s]||a<=1&&["m"]||a<r.m&&["mm",a]||l<=1&&["h"]||l<r.h&&["hh",l]||o<=1&&["d"]||o<r.d&&["dd",o];return r.w!=null&&(h=h||c<=1&&["w"]||c<r.w&&["ww",c]),h=h||d<=1&&["M"]||d<r.M&&["MM",d]||u<=1&&["y"]||["yy",u],h[2]=t,h[3]=+e>0,h[4]=n,Wl.apply(null,h)}function jl(e){return e===void 0?pe:typeof e=="function"?(pe=e,!0):!1}function ql(e,t){return Ye[e]===void 0?!1:t===void 0?Ye[e]:(Ye[e]=t,e==="s"&&(Ye.ss=t-1),!0)}function Gl(e,t){if(!this.isValid())return this.localeData().invalidDate();var r=!1,n=Ye,i,s;return typeof e=="object"&&(t=e,e=!1),typeof e=="boolean"&&(r=e),typeof t=="object"&&(n=Object.assign({},Ye,t),t.s!=null&&t.ss==null&&(n.ss=t.s-1)),i=this.localeData(),s=Vl(this,!r,n,i),r&&(s=i.pastFuture(+this,s)),i.postformat(s)}var lr=Math.abs;function Le(e){return(e>0)-(e<0)||+e}function Ot(){if(!this.isValid())return this.localeData().invalidDate();var e=lr(this._milliseconds)/1e3,t=lr(this._days),r=lr(this._months),n,i,s,a,l=this.asSeconds(),o,d,c,u;return l?(n=K(e/60),i=K(n/60),e%=60,n%=60,s=K(r/12),r%=12,a=e?e.toFixed(3).replace(/\.?0+$/,""):"",o=l<0?"-":"",d=Le(this._months)!==Le(l)?"-":"",c=Le(this._days)!==Le(l)?"-":"",u=Le(this._milliseconds)!==Le(l)?"-":"",o+"P"+(s?d+s+"Y":"")+(r?d+r+"M":"")+(t?c+t+"D":"")+(i||n||e?"T":"")+(i?u+i+"H":"")+(n?u+n+"M":"")+(e?u+a+"S":"")):"P0D"}var $=kt.prototype;$.isValid=Ba;$.abs=wl;$.add=_l;$.subtract=Sl;$.as=Tl;$.asMilliseconds=wi;$.asSeconds=xl;$.asMinutes=Dl;$.asHours=Cl;$.asDays=El;$.asWeeks=kl;$.asMonths=Nl;$.asQuarters=Ol;$.asYears=Al;$.valueOf=Fl;$._bubble=Ml;$.clone=Rl;$.get=Pl;$.milliseconds=$l;$.seconds=Ll;$.minutes=Il;$.hours=Yl;$.days=Bl;$.weeks=Hl;$.months=Ul;$.years=zl;$.humanize=Gl;$.toISOString=Ot;$.toString=Ot;$.toJSON=Ot;$.locale=oi;$.localeData=di;$.toIsoString=Q("toIsoString() is deprecated. Please use toISOString() instead (notice the capitals)",Ot);$.lang=li;x("X",0,0,"unix");x("x",0,0,"valueOf");b("x",xt);b("X",ms);Y("X",function(e,t,r){r._d=new Date(parseFloat(e)*1e3)});Y("x",function(e,t,r){r._d=new Date(P(e))});y.version="2.30.1";Gi(U);y.fn=m;y.min=$a;y.max=La;y.now=Ia;y.utc=de;y.unix=fl;y.months=gl;y.isDate=rt;y.locale=Ce;y.invalid=_t;y.duration=ne;y.isMoment=re;y.weekdays=yl;y.parseZone=ml;y.localeData=Se;y.isDuration=mt;y.monthsShort=pl;y.weekdaysMin=bl;y.defineLocale=Nr;y.updateLocale=fa;y.locales=ma;y.weekdaysShort=vl;y.normalizeUnits=X;y.relativeTimeRounding=jl;y.relativeTimeThreshold=ql;y.calendarFormat=co;y.prototype=m;y.HTML5_FMT={DATETIME_LOCAL:"YYYY-MM-DDTHH:mm",DATETIME_LOCAL_SECONDS:"YYYY-MM-DDTHH:mm:ss",DATETIME_LOCAL_MS:"YYYY-MM-DDTHH:mm:ss.SSS",DATE:"YYYY-MM-DD",TIME:"HH:mm",TIME_SECONDS:"HH:mm:ss",TIME_MS:"HH:mm:ss.SSS",WEEK:"GGGG-[W]WW",MONTH:"YYYY-MM"};class Kl{static numeroALetras(t){if(t=parseInt(t),isNaN(t)||t<0||t>1e6)return"Número fuera de rango";const r=["cero","uno","dos","tres","cuatro","cinco","seis","siete","ocho","nueve"],n=["","","veinte","treinta","cuarenta","cincuenta","sesenta","setenta","ochenta","noventa"],i={10:"diez",11:"once",12:"doce",13:"trece",14:"catorce",15:"quince",16:"dieciséis",17:"diecisiete",18:"dieciocho",19:"diecinueve"},s=["","cien","doscientos","trescientos","cuatrocientos","quinientos","seiscientos","setecientos","ochocientos","novecientos"];function a(u){if(u<10)return r[u];if(u>=10&&u<20)return i[u];if(u<100){const f=u%10;return`${n[Math.floor(u/10)]}${f>0?" y "+r[f]:""}`}if(u===100)return"cien";const h=u%100;return`${s[Math.floor(u/100)]}${h>0?" "+a(h):""}`}if(t===1e6)return"un millón";let l=Math.floor(t/1e3),o=t%1e3,d=l>0?l===1?"mil":`${a(l)} mil`:"",c=o>0?a(o):"";return(d+" "+c).trim()}static imprimirCaja(t){}static async factura(t){return new Promise(async(r,n)=>{try{const i=me.conversorNumerosALetras,s=new i,a=se().env,l=v=>Number(v||0).toFixed(2),o=v=>(v??"").toString(),d=Number(t.total??t.montoTotal??0),c=t.numeroFactura??t.numero_factura??t.id??"—",u=t.fechaEmision??(t.fecha&&t.hora?`${t.fecha} ${t.hora}`:"—"),h=t.nombre??t?.cliente?.nombre??"SN",f=t.complemento??t?.cliente?.complemento??"",g=t.ci??t?.cliente?.ci??"0",_=t.cliente_id??t?.cliente?.id??"—",M=a?.puntoVenta??0,p=t.cuf??null,D=p?p.match(/.{1,20}/g).join("<br>"):null,E=p?"FACTURA<br>CON DERECHO A CRÉDITO FISCAL":"NOTA DE VENTA",w=t.leyenda??"Ley N° 453: Puedes acceder a la reclamación cuando tus derechos han sido vulnerados.",B=Array.isArray(t.venta_detalles)?t.venta_detalles:Array.isArray(t.details)?t.details:[],T=Math.floor(d),N=Math.round((d-T)*100).toString().padStart(2,"0"),k=`Son ${s.convertToText(T)} ${N}/100 Bolivianos`;let S=null;D&&(S=await fe.toDataURL(`${a.url2}consulta/QR?nit=${a.nit}&cuf=${D}&numero=${c}&t=2`,{errorCorrectionLevel:"M",type:"png",width:110,margin:0,color:{dark:"#000",light:"#FFF"}}));let O=`${this.head()}
<style>
/* Ticket 80mm ~ 300px */
.ticket { width:300px; margin:0 auto; }
.mono { font-family: "Courier New", Courier, monospace; }
.fs9 { font-size:9px; } .fs10{font-size:10px;} .fs11{font-size:11px;} .fs12{font-size:12px;}
.center{ text-align:center; } .right{ text-align:right; } .left{ text-align:left; }
hr{ border:0; border-top:1px dashed #000; margin:6px 0; }
.title{ font-weight:700; text-transform:uppercase; line-height:1.15; }
.small { font-size:8px; line-height:1.25; }

.tbl{ width:100%; border-collapse:collapse; }
.tbl td{ padding:2px 0; vertical-align:top; }

.lbl{ width:135px; font-weight:700; text-transform:uppercase; }
.val{ width:auto; }

.det-header{ font-weight:700; text-transform:uppercase; margin:4px 0; }
.item-desc{ font-weight:700; }
.item-meta{ color:#111; }

.tot td{ padding:1px 0; }
.tot .l{ width:70%; }
.tot .r{ width:30%; text-align:right; }

.qr{ display:flex; justify-content:center; margin-top:6px; }
@page { margin: 6mm; }
</style>

<div class="ticket mono fs10">
  <div class="title fs12 center">${E}</div>

  <div class="center small">
    ${o(a.razon)}<br>
    Casa Matriz<br>
    No. Punto de Venta ${M}<br>
    ${o(a.direccion)}<br>
    Tel. ${o(a.telefono)}<br>
    Oruro
  </div>

  <hr>

  <table class="tbl fs10">
    <tr><td class="lbl">NIT</td><td class="val">${o(a.nit)}</td></tr>
    <tr><td class="lbl">FACTURA N°</td><td class="val">${c}</td></tr>
    <tr><td class="lbl">CÓD. AUTORIZACIÓN</td><td class="val">${D??"—"}</td></tr>
  </table>

  <hr>

  <table class="tbl fs10">
    <tr><td class="lbl">NOMBRE/RAZÓN SOCIAL</td><td class="val">${o(h)}</td></tr>
    <tr><td class="lbl">NIT/CI/CEX</td><td class="val">${o(g)}${o(f?"-"+f:"")}</td></tr>
    <tr><td class="lbl">NRO. CLIENTE</td><td class="val">${o(_)}</td></tr>
    <tr><td class="lbl">FECHA DE EMISIÓN</td><td class="val">${o(u)}</td></tr>
  </table>

  <hr>
  <div class="det-header center">DETALLE</div>`;B.forEach(v=>{const F=v.producto_id??v.product_id??v?.producto?.id??"—",ie=o(v.nombre??v.descripcion??v?.producto?.nombre??""),Ee=o(v.unidad??v?.producto?.unidad??""),at=Number(v.cantidad??v.qty??0),je=Number(v.precio??v.precioUnitario??0),ot=Number(v.descuento??v.montoDescuento??0),qe=v.subTotal??at*je-ot;O+=`
      <table class="tbl fs10">
        <tr>
          <td class="left item-desc" colspan="3">${F} - ${ie}</td>
          <td class="right item-desc">${l(qe)}</td>
        </tr>
        <tr><td class="left item-meta" colspan="4">Unidad de Medida: ${Ee||"Unidad (Servicios)"}</td></tr>
        <tr>
          <td class="right" style="width:22%;">${l(at)}</td>
          <td class="center" style="width:6%;">x</td>
          <td class="right" style="width:32%;">${l(je)} - ${l(ot)}</td>
          <td class="right" style="width:40%;"></td>
        </tr>
      </table>`}),O+=`
  <hr>
  <table class="tbl tot fs10">
    <tr><td class="l left strong">TOTAL Bs</td><td class="r strong">${l(d)}</td></tr>
    <tr><td class="l left">(-) DESCUENTO Bs</td><td class="r">0.00</td></tr>
    <tr><td class="l left strong">SUBTOTAL A PAGAR Bs</td><td class="r strong">${l(d)}</td></tr>
    <tr><td class="l left">(-) AJUSTES NO SUJETOS A IVA Bs</td><td class="r">0.00</td></tr>
    <tr><td class="l left strong">MONTO TOTAL A PAGAR Bs</td><td class="r strong">${l(d)}</td></tr>
    <tr><td class="l left">(-) TASAS Bs</td><td class="r">0.00</td></tr>
    <tr><td class="l left">(-) OTROS PAGOS NO SUJETO IVA Bs</td><td class="r">0.00</td></tr>
    <tr><td class="l left">(+) AJUSTES NO SUJETOS A IVA Bs</td><td class="r">0.00</td></tr>
    <tr><td class="l left strong">IMPORTE BASE CRÉDITO FISCAL</td><td class="r strong">${l(d)}</td></tr>
  </table>

  <div class="fs10" style="margin-top:6px;">${k}</div>

  <hr>
  <div class="center small">
    ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS,<br>
    EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE DE ACUERDO A LEY
  </div>
  <div class="center small" style="margin-top:4px;">${o(w)}</div>
  <div class="center small" style="margin-top:4px;">“Este documento es la Representación Gráfica de un<br>Documento Fiscal Digital emitido en una modalidad de facturación en línea”</div>
  ${S?`<div class="qr"><img src="${S}" alt="QR"></div>`:""}
</div>`;const C=document.getElementById("myElement");C&&(C.innerHTML=O),new J.Printd().print(C),r(S)}catch(i){n(i)}})}static nota(t,r=!0){return console.log("factura",t),new Promise((n,i)=>{const s=this.numeroALetras(123),a={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}};se().env,fe.toDataURL(`Fecha: ${t.fecha_emision} Monto: ${parseFloat(t.total).toFixed(2)}`,a).then(l=>{let o="",d="";t.producto&&(o="<tr><td class='titder'>PRODUCTO:</td><td class='contenido'>"+t.producto+"</td></tr>"),t.cantidad&&(d="<tr><td class='titder'>CANTIDAD:</td><td class='contenido'>"+t.cantidad+"</td></tr>");let c=`${this.head()}
  <!--div style='padding-left: 0.5cm;padding-right: 0.5cm'>
  <img src="logo.png" alt="logo" style="width: 100px; height: 50px; display: block; margin-left: auto; margin-right: auto;">
      <div class='titulo'>${t.tipo_venta==="EGRESO"?"NOTA DE EGRESO":"NOTA DE VENTA"}</div>
      <div class='titulo2'>${t.tipo_comprobante} <br>
      Casa Matriz<br>
      No. Punto de Venta 0<br>
Calle Beni Nro. 60, entre 6 de Octubre y Potosí.<br>
Tel. 25247993 - 76148555<br>
Oruro</div!-->
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
   .mono {
    font-family: Monospace,serif !important;
    font-size: 18px !important;
  }
</style>
<title></title>
</head>
<body>
<div class="mono">
<hr>
<table>
<tr><td class='titder'>ID:</td><td class='titder'>${t.id}</td></tr>
<tr><td class='titder'>NOMBRE/RAZÓN SOCIAL:</td><td class='titder'>${t.nombre}</td></tr>
<tr><!-- td class='titder'>NIT/CI/CEX:</td><td class='contenido'>${t.client?t.client.nit:""}</td --></tr>
<tr><td class='titder'>FECHA DE EMISIÓN:</td><td class='contenido'>${t.fecha}</td></tr>
${o}
${d}
</table><hr><div class='titulo'>DETALLE</div>`;t.venta_detalles.forEach(u=>{console.log("r",u),c+=`<div style='font-size: 12px'><b> ${u.producto?.nombre} </b></div>`,u.visible===1?c+=`<div>
                    <span style='font-size: 18px;font-weight: bold'>
                        ${u.cantidad}
                    </span>
                    <span>
                    ${parseFloat(u.precio).toFixed(2)}
                    </span>

                    <span style='float:right'>
                        ${parseFloat(u.precio*u.cantidad).toFixed(2)}
                    </span>
                    </div>`:c+=`<div>
                    <span style='font-size: 18px;font-weight: bold'>
                        ${u.cantidad}
                    </span>
                    <span>

                    </span>

                    <span style='float:right'>

                    </span>`}),c+=`<hr>
<div>${t.comentario===""||t.comentario===null||t.comentario===void 0?"":"Comentario: "+t.comentario}</div>
      <table style='font-size: 8px;'>
      <tr><td class='titder' style='width: 60%'>SUBTOTAL Bs</td><td class='titder'>${parseFloat(t.total).toFixed(2)}</td></tr>
<!--      <tr><td class='titder' style='width: 60%'>Descuento Bs</td><td class='titder'>${parseFloat(t.descuento).toFixed(2)}</td></tr>-->
<!--      <tr><td class='titder' style='width: 60%'>TOTAL Bs</td><td class='titder'>${parseFloat(t.total-t.descuento).toFixed(2)}</td></tr>-->
      </table>
      <br>
      <div>Son ${s} ${((parseFloat(t.total)-Math.floor(parseFloat(t.total)))*100).toFixed(2)} /100 Bolivianos</div><hr>
        <!--div style='display: flex;justify-content: center;'>
          <img  src="${l}" style="width: 75px; height: 75px; display: block; margin-left: auto; margin-right: auto;">
        </div--!>
      </div>
      </div>
</body>
</html>`,document.getElementById("myElement").innerHTML=c,r&&new J.Printd().print(document.getElementById("myElement")),n(l)}).catch(l=>{i(l)})})}static cotizacion(t,r,n,i,s=!0){return(i==null||i==="")&&(i=0),new Promise((a,l)=>{const o=me.conversorNumerosALetras,c=new o().convertToText(parseInt(n)),u=y().format("YYYY-MM-DD"),h={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}},f=se().env;fe.toDataURL(`Fecha: ${u} Monto: ${parseFloat(n).toFixed(2)}`,h).then(g=>{let _=`${this.head()}
  <div style='padding-left: 0.5cm;padding-right: 0.5cm'>
  <img src="logo.png" alt="logo" style="width: 100px; height: 50px; display: block; margin-left: auto; margin-right: auto;">
      <div class='titulo'>COTIZACION</div>
      <div class='titulo2'>${f.razon} <br>
      Casa Matriz<br>
      No. Punto de Venta 0<br>
${f.direccion}<br>
Tel. ${f.telefono}<br>
Oruro</div>
<hr>
<table>
<tr><td class='titder'>NOMBRE/RAZÓN SOCIAL:</td><td class='contenido'>${r.nombre}</td>
<tr><td class='titder'>FECHA DE EMISIÓN:</td><td class='contenido'>${u}</td></tr>
</table><hr><div class='titulo'>DETALLE</div>`;t.forEach(M=>{_+=`<div style='font-size: 12px'><b> ${M.nombre} </b></div>`,_+=`<div><span style='font-size: 18px;font-weight: bold'>${M.cantidadVenta}</span> ${parseFloat(M.precioVenta).toFixed(2)} 0.00
                    <span style='float:right'>${parseFloat(M.precioVenta*M.cantidadVenta).toFixed(2)}</span></div>`}),_+=`<hr>
<div>${r.comentario===""||r.comentario===null||r.comentario===void 0?"":"Comentario: "+r.comentario}</div>
      <table style='font-size: 8px;'>
      <tr><td class='titder' style='width: 60%'>SUBTOTAL Bs</td><td class='conte2'>${parseFloat(n).toFixed(2)}</td></tr>
      <tr><td class='titder' style='width: 60%'>Descuento Bs</td><td class='conte2'>${parseFloat(i).toFixed(2)}</td></tr>
      <tr><td class='titder' style='width: 60%'>TOTAL Bs</td><td class='conte2'>${parseFloat(n-i).toFixed(2)}</td></tr>
      </table>
      <br>
      <div>Son ${c} ${((parseFloat(n)-Math.floor(parseFloat(n)))*100).toFixed(2)} /100 Bolivianos</div><hr>
      <div style='display: flex;justify-content: center;'>
        <img  src="${g}" style="width: 75px; height: 75px; display: block; margin-left: auto; margin-right: auto;">
      </div></div>
      </div>
</body>
</html>`,document.getElementById("myElement").innerHTML=_,s&&new J.Printd().print(document.getElementById("myElement")),a(g)}).catch(g=>{l(g)})})}static notaCompra(t){return console.log("factura",t),new Promise((r,n)=>{const i=me.conversorNumerosALetras,a=new i().convertToText(parseInt(t.total)),l={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}},o=se().env;fe.toDataURL(`Fecha: ${t.fecha_emision} Monto: ${parseFloat(t.total).toFixed(2)}`,l).then(async d=>{let c=`${this.head()}
  <div style='padding-left: 0.5cm;padding-right: 0.5cm'>
  <img src="logo.png" alt="logo" style="width: 100px; height: 50px; display: block; margin-left: auto; margin-right: auto;">
      <div class='titulo'>${t.tipo_venta==="EGRESO"?"NOTA DE EGRESO":"NOTA DE COMPRA"}</div>
      <div class='titulo2'>${o.razon} <br>
      Casa Matriz<br>
      No. Punto de Venta 0<br>
${o.direccion}<br>
Tel. ${o.telefono}<br>
Oruro</div>
<hr>
<table>
<tr><td class='titder'>NOMBRE/RAZÓN SOCIAL:</td><td class='contenido'>${t.client?t.client.nombre:""}</td>
</tr><tr><td class='titder'>NIT/CI/CEX:</td><td class='contenido'>${t.client?t.client.nit:""}</td></tr>
<!--<tr><td class='titder'>FECHA DE EMISIÓN:</td><td class='contenido'>${t.fecha_emision}</td></tr>-->
</table><hr><div class='titulo'>DETALLE</div>`;t.buy_details.forEach(h=>{c+=`<div style='font-size: 12px'><b>${h.nombre} </b></div>`,c+=`<div><span style='font-size: 14px;font-weight: bold'>${h.cantidad}</span> ${parseFloat(h.precio).toFixed(2)} 0.00
                    <span style='float:right'>${parseFloat(h.subtotal).toFixed(2)}</span></div>`}),c+=`<hr>
      <table style='font-size: 8px;'>
      <tr><td class='titder' style='width: 60%'>SUBTOTAL Bs</td><td class='conte2'>${parseFloat(t.total).toFixed(2)}</td></tr>
      <tr><td class='titder' style='width: 60%'>Descuento Bs</td><td class='conte2'>${parseFloat(t.descuento).toFixed(2)}</td></tr>
      <tr><td class='titder' style='width: 60%'>TOTAL Bs</td><td class='conte2'>${parseFloat(t.total-t.descuento).toFixed(2)}</td></tr>
      </table>
      <br>
      <div>Son ${a} ${((parseFloat(t.total)-Math.floor(parseFloat(t.total)))*100).toFixed(2)} /100 Bolivianos</div><hr>
      <div style='display: flex;justify-content: center;'>
        <img  src="${d}" style="width: 75px; height: 75px; display: block; margin-left: auto; margin-right: auto;">
      </div></div>
      </div>
</body>
</html>`,document.getElementById("myElement").innerHTML=c,new J.Printd().print(document.getElementById("myElement")),r(d)}).catch(d=>{n(d)})})}static reportTotal(t,r){const n=t.filter(a=>a.tipoVenta==="Ingreso").reduce((a,l)=>a+l.montoTotal,0),i=t.filter(a=>a.tipoVenta==="Egreso").reduce((a,l)=>a+l.montoTotal,0),s=n-i;return console.log("montoTotal",s),new Promise((a,l)=>{const o=me.conversorNumerosALetras,d=new o,c=Math.abs(s),u=d.convertToText(parseInt(c)),h={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}},f=se().env;fe.toDataURL(` Monto: ${parseFloat(s).toFixed(2)}`,h).then(g=>{let _=`${this.head()}
  <div style='padding-left: 0.5cm;padding-right: 0.5cm'>
  <img src="logo.png" alt="logo" style="width: 100px; height: 100px; display: block; margin-left: auto; margin-right: auto;">
      <div class='titulo'>title</div>
      <div class='titulo2'>${f.razon} <br>
      Casa Matriz<br>
      No. Punto de Venta 0<br>
${f.direccion}<br>
Tel. ${f.telefono}<br>
Oruro</div>
<hr>
<table>
</table><hr><div class='titulo'>DETALLE</div>`;t.forEach(p=>{_+=`<div style='font-size: 12px'><b> ${p.user.name} </b></div>`,_+=`<div> ${parseFloat(p.montoTotal).toFixed(2)} ${p.tipoVenta}
          <span style='float:right'> ${p.tipoVenta==="Egreso"?"-":""} ${parseFloat(p.montoTotal).toFixed(2)}</span></div>`}),_+=`<hr>
      <table style='font-size: 8px;'>
      <tr><td class='titder' style='width: 60%'>SUBTOTAL Bs</td><td class='conte2'>${parseFloat(s).toFixed(2)}</td></tr>
      </table>
      <br>
      <div>Son ${u} ${((parseFloat(s)-Math.floor(parseFloat(s)))*100).toFixed(2)} /100 Bolivianos</div><hr>
      <div style='display: flex;justify-content: center;'>
        <img  src="${g}" style="width: 75px; height: 75px; display: block; margin-left: auto; margin-right: auto;">
      </div></div>
      </div>
</body>
</html>`,document.getElementById("myElement").innerHTML=_,new J.Printd().print(document.getElementById("myElement")),a(g)}).catch(g=>{l(g)})})}static reciboCompra(t){return new Promise((r,n)=>{const i=me.conversorNumerosALetras,a=new i().convertToText(parseInt(t.total)),l={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}},o=se().env;console.log("env",o),fe.toDataURL(`Fecha: ${t.date} Monto: ${parseFloat(t.total).toFixed(2)}`,l).then(d=>{let c=`${this.head()}
    <div style='padding-left: 0.5cm;padding-right: 0.5cm'>
    <img src="logo.png" alt="logo" style="width: 100px; height: 100px; display: block; margin-left: auto; margin-right: auto;">
      <div class='titulo'>RECIBO DE COMPRA</div>
      <div class='titulo2'>${o.razon} <br>
      Casa Matriz<br>
      No. Punto de Venta 0<br>
    ${o.direccion}<br>
    Tel. ${o.telefono}<br>
    Oruro</div>
    <hr>
    <table>
    </table><hr><div class='titulo'>DETALLE</div>`;t.compra_detalles.forEach(h=>{c+=`<div style='font-size: 12px'><b>${h.nombre} </b></div>`,c+=`<div>${h.cantidad} ${parseFloat(h.precio).toFixed(2)} 0.00
          <span style='float:right'>${parseFloat(h.total).toFixed(2)}</span></div>`}),c+=`<hr>
      <table style='font-size: 8px;'>
      <tr><td class='titder' style='width: 60%'>SUBTOTAL Bs</td><td class='conte2'>${parseFloat(t.total).toFixed(2)}</td></tr>
      </table>
      <br>
      <div>Son ${a} ${((parseFloat(t.total)-Math.floor(parseFloat(t.total)))*100).toFixed(2)} /100 Bolivianos</div><hr>
      <div style='display: flex;justify-content: center;'>
        <img  src="${d}" style="width: 75px; height: 75px; display: block; margin-left: auto; margin-right: auto;">
      </div></div>
      </div>
    </body>
    </html>`,document.getElementById("myElement").innerHTML=c,new J.Printd().print(document.getElementById("myElement")),r(d)}).catch(d=>{n(d)})})}static reciboPedido(t){return console.log("reciboPedido",t),new Promise((r,n)=>{const i=me.conversorNumerosALetras,a=new i().convertToText(parseInt(t.total)),l={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}},o=se().env;fe.toDataURL(`Fecha: ${t.date} Monto: ${parseFloat(t.total).toFixed(2)}`,l).then(d=>{let c=`${this.head()}
    <div style='padding-left: 0.5cm;padding-right: 0.5cm'>
    <img src="logo.png" alt="logo" style="width: 100px; height: 100px; display: block; margin-left: auto; margin-right: auto;">
      <div class='titulo'>RECIBO DE PEDIDO</div>
      <div class='titulo2'>${o.razon} <br>
      Casa Matriz<br>
      No. Punto de Venta 0<br>
    ${o.direccion}<br>
    Tel. ${o.telefono}<br>
    Oruro</div>
    <hr>
    <div style='display: flex;justify-content: space-between;'>
        <div class='titulo'>FECHA HORA</div>
        <div class='titulo2'>${t.fecha} ${t.hora}</div>
    </div>
    <div style='display: flex;justify-content: space-between;'>
        <div class='titulo'>ID</div>
        <div class='titulo2'>${t.id}</div>
    </div>
    <hr>
    <div class='titulo'>DETALLE</div>`;t.detalles.forEach(h=>{c+=`<div style='font-size: 12px'><b>${h.producto?.nombre} </b></div>`,c+=`<div>${h.cantidad} ${parseFloat(h.cantidad).toFixed(2)} 0.00
          <span style='float:right'>${parseFloat(h.cantidad).toFixed(2)}</span></div>`}),c+=`<hr>
      <table style='font-size: 8px;'>
      <tr><td class='titder' style='width: 60%'>SUBTOTAL Bs</td><td class='conte2'>${parseFloat(t.total).toFixed(2)}</td></tr>
      </table>
      <br>
      <div>Son ${a} ${((parseFloat(t.total)-Math.floor(parseFloat(t.total)))*100).toFixed(2)} /100 Bolivianos</div><hr>
      <div style='display: flex;justify-content: center;'>
        <img  src="${d}" style="width: 75px; height: 75px; display: block; margin-left: auto; margin-right: auto;">
      </div></div>
      </div>
    </body>
    </html>`,document.getElementById("myElement").innerHTML=c,new J.Printd().print(document.getElementById("myElement")),r(d)}).catch(d=>{n(d)})})}static reciboTranferencia(t,r,n,i){return console.log("producto",t,"de",r,"ha",n,"cantidad",i),new Promise((s,a)=>{const l=me.conversorNumerosALetras,d=new l().convertToText(parseInt(i)),c={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}},u=se().env;fe.toDataURL(`de: ${r} A: ${n}`,c).then(h=>{let f=`${this.head()}
    <div style='padding-left: 0.5cm;padding-right: 0.5cm'>
    <img src="logo.png" alt="logo" style="width: 100px; height: 100px; display: block; margin-left: auto; margin-right: auto;">
      <div class='titulo'>RECIBO DE TRANSFERENCIA</div>
      <div class='titulo2'>${u.razon} <br>
      Casa Matriz<br>
      No. Punto de Venta 0<br>
    ${u.direccion}<br>
    Tel. ${u.telefono}<br>
    Oruro</div>
    <hr>
    <table>
    </table><hr><div class='titulo'>DETALLE</div>`;f+=`<div style='font-size: 12px'><b>Producto: ${t} de Sucursal${r} a ${n} </b></div>`,f+=`<hr>
      <table style='font-size: 8px;'>
      <tr><td class='titder' style='width: 60%'>CANTIDAD </td><td class='conte2'>${i+""}</td></tr>
      </table>
      <br>
      <div>Son ${d+""} ${i+""} unidades</div><hr>
      <div style='display: flex;justify-content: center;'>
        <img  src="${h}" style="width: 75px; height: 75px; display: block; margin-left: auto; margin-right: auto;">
      </div></div>
      </div>
    </body>
    </html>`,document.getElementById("myElement").innerHTML=f,new J.Printd().print(document.getElementById("myElement")),s(h)}).catch(h=>{a(h)})})}static head(){return`<html>
<style>
      .titulo{
      font-size: 12px;
      text-align: center;
      font-weight: bold;
      }
      .titulo2{
      font-size: 10px;
      text-align: center;
      }
            .titulo3{
      font-size: 10px;
      text-align: center;
      width:70%;
      }
            .contenido{
      font-size: 10px;
      text-align: left;
      }
      .conte2{
      font-size: 10px;
      text-align: right;
      }
      .titder{
      font-size: 12px;
      text-align: right;
      font-weight: bold;
      }
      hr{
  border-top: 1px dashed   ;
}
  table{
    width:100%
  }
  h1 {
    color: black;
    font-family: sans-serif;
  }
  </style>
<body>
<div style="width: 300px;">`}static async printFactura(t){const r=me.conversorNumerosALetras,i=new r().convertToText(parseInt(t.total)),s=se().env,a=await fe.toDataURL(`${s.url2}consulta/QR?nit=${s.nit}&cuf=${t.cuf}&numero=${t.id}&t=2`,{errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}}),l=t.online?"en":"fuera de";let o=`<style>
    .titulo { font-size: 12px; text-align: center; font-weight: bold; }
    .titulo2 { font-size: 10px; text-align: center; }
    .contenido { font-size: 10px; text-align: left; }
    .conte2 { font-size: 10px; text-align: right; }
    .titder { font-size: 12px; text-align: right; font-weight: bold; }
    hr { border-top: 1px dashed; }
  </style>
  <div style='padding: 0.5cm'>
    <div class='titulo'>FACTURA CON DERECHO A CREDITO FISCAL</div>
    <div class='titulo2'>
      ${s.razon}<br>Casa Matriz<br>No. Punto de Venta 0<br>
      ${s.direccion}<br>Tel. ${s.telefono}<br>Oruro
    </div>
    <hr>
    <div class='titulo'>NIT</div><div class='titulo2'>${s.nit}</div>
    <div class='titulo'>FACTURA N°</div><div class='titulo2'>${t.id}</div>
    <div class='titulo'>CÓD. AUTORIZACIÓN</div><div class='titulo2'>${t.cuf}</div>
    <hr>
    <table>
      <tr><td class='titder'>NOMBRE/RAZÓN SOCIAL:</td><td class='contenido'>${t.nombre}</td></tr>
      <tr><td class='titder'>NIT/CI/CEX:</td><td class='contenido'>${t.ci}${t.cliente?.complemento?"-"+t.cliente?.complemento:""}</td></tr>
      <tr><td class='titder'>COD. CLIENTE:</td><td class='contenido'>${t.cliente.id}</td></tr>
      <tr><td class='titder'>FECHA DE EMISIÓN:</td><td class='contenido'>${t.fecha}</td></tr>
    </table>
    <hr>
    <div class='titulo'>DETALLE</div>`;t.venta_detalles.forEach(u=>{o+=`<div style='font-size: 12px'><b>${u.id} - ${u.nombre}</b></div>
             <div>${u.cantidad} ${parseFloat(u.precio).toFixed(2)} 0.00
             <span style='float:right'>${parseFloat(u.cantidad*u.precio).toFixed(2)}</span></div>`}),o+=`<hr>
    <table style='font-size: 8px;'>
      <tr><td class='titder'>SUBTOTAL Bs</td><td class='conte2'>${parseFloat(t.total).toFixed(2)}</td></tr>
      <tr><td class='titder'>DESCUENTO Bs</td><td class='conte2'>0.00</td></tr>
      <tr><td class='titder'>TOTAL Bs</td><td class='conte2'>${parseFloat(t.total).toFixed(2)}</td></tr>
      <tr><td class='titder'>MONTO GIFT CARD Bs</td><td class='conte2'>0.00</td></tr>
      <tr><td class='titder'>MONTO A PAGAR Bs</td><td class='conte2'>${parseFloat(t.total).toFixed(2)}</td></tr>
      <tr><td class='titder'>IMPORTE BASE CRÉDITO FISCAL Bs</td><td class='conte2'>${parseFloat(t.total).toFixed(2)}</td></tr>
    </table><br>
    <div>Son ${i} ${((parseFloat(t.total)-Math.floor(t.total))*100).toFixed(0)}/100 Bolivianos</div>
    <hr>
    <div class='titulo2' style='font-size: 9px'>ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS,<br>
    EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE DE ACUERDO A LEY<br><br>
    ${t.leyenda}<br><br>
    “Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido en una modalidad de facturación ${l} línea”</div>
    <div style='display: flex; justify-content: center;'>
      <img src="${a}" />
    </div>
  </div>`;const d=document.getElementById("myElement");d&&(d.innerHTML=o),new J.Printd().print(d)}static async reciboVentaSimple(t,r=!0){try{const n=se().env||{},i=me.conversorNumerosALetras,s=new i,a=M=>Number(M||0).toFixed(2),l=(M,p="")=>(M??p).toString(),o=Number(t.total??0),d=Math.floor(o),c=Math.round((o-d)*100).toString().padStart(2,"0"),h=`Son ${s.convertToText(d)} ${c}/100 Bolivianos`,f=Array.isArray(t.venta_detalles)?t.venta_detalles:[];let g=`${this.head()}
  <div style='padding-left: 0.5cm;padding-right: 0.5cm'>
    <img src="logo.png" alt="logo" style="width: 100px; height: 100px; display: block; margin-left: auto; margin-right: auto;">

    <div class='titulo'>RECIBO DE VENTA</div>
    <div class='titulo2'>
      ${l(n.razon)} <br>
      Casa Matriz<br>
      No. Punto de Venta ${l(n.puntoVenta??0)}<br>
      ${l(n.direccion)}<br>
      Tel. ${l(n.telefono)}<br>
      Oruro
    </div>

    <hr>

    <div style="display:flex; justify-content: space-between;">
      <div class="titder" style="width:45%;">FECHA HORA</div>
      <div class="conte2" style="width:55%;">${l(t.fecha)} ${l(t.hora)}</div>
    </div>

    <div style="display:flex; justify-content: space-between;">
      <div class="titder" style="width:45%;">ID</div>
      <div class="conte2" style="width:55%;">${l(t.id)}</div>
    </div>

    <div style="display:flex; justify-content: space-between;">
      <div class="titder" style="width:45%;">USUARIO</div>
      <div class="conte2" style="width:55%;">${l(t.user?.name)}</div>
    </div>

    <div style="display:flex; justify-content: space-between;">
      <div class="titder" style="width:45%;">PAGO</div>
      <div class="conte2" style="width:55%;">${l(t.tipo_pago??t.tipoPago??t.metodo_pago??"")}</div>
    </div>

    ${t.tipo_venta?`
    <div style="display:flex; justify-content: space-between;">
      <div class="titder" style="width:45%;">TIPO</div>
      <div class="conte2" style="width:55%;">${l(t.tipo_venta)}</div>
    </div>`:""}

    <hr>
    <div class='titulo'>DETALLE</div>
`;f.forEach(M=>{const p=l(M.producto?.nombre??M.nombre??M.descripcion??""),D=Number(M.cantidad??0),E=Number(M.precio??0),w=Number(M.subTotal??D*E),B=l(M.producto_id??M.product_id??M.producto?.id??"");g+=`<div style='font-size: 12px'><b>${B?B+" - ":""}${p}</b></div>`,g+=`
      <div>
        <span style='font-size: 14px;font-weight: bold'>${a(D)}</span>
        <span>${a(E)} 0.00</span>
        <span style='float:right'>${a(w)}</span>
      </div>`}),g+=`
    <hr>
    <table style='font-size: 8px;'>
      <tr>
        <td class='titder' style='width: 60%'>SUBTOTAL Bs</td>
        <td class='conte2'>${a(o)}</td>
      </tr>
    </table>

    <br>
    <div>${h}</div>
    <hr>

    <div class='titulo2' style="font-size: 9px">
      ¡Gracias por su compra!
    </div>

  </div>
</div>
</body>
</html>`;const _=document.getElementById("myElement");return _&&(_.innerHTML=g),r&&new J.Printd().print(document.getElementById("myElement")),!0}catch(n){throw console.error("reciboVentaSimple error:",n),n}}static ensureMount(){let t=document.getElementById("myElement");return t||(t=document.createElement("div"),t.id="myElement",t.style.display="none",document.body.appendChild(t)),t}static printTicketHtml(t){const r=this.ensureMount();r.innerHTML=t,new J.Printd().print(r)}static printTicketHtmlConImagenes(t,r=4e3){const n=this.ensureMount();n.innerHTML=t;const i=new J.Printd,s=Array.from(n.querySelectorAll("img"));if(!s.length){i.print(n);return}let a=!1,l=s.length;const o=()=>{a||(a=!0,i.print(n))};s.forEach(d=>{if(d.complete){l-=1,l<=0&&o();return}d.addEventListener("load",()=>{l-=1,l<=0&&o()}),d.addEventListener("error",()=>{l-=1,l<=0&&o()})}),setTimeout(o,r)}static fichaDespacho(t){const r=new Date(t.created_at||Date.now()),n=String(r.getDate()).padStart(2,"0"),i=String(r.getMonth()+1).padStart(2,"0"),s=r.getFullYear(),a=`${n}/${i}/${s}`,l=t.detalles||[],o=t.prestamos||[],d=t.user?.name||t.user?.username||"-",c=t.cliente_nombre||t.cliente?.nombre||"-",u=t.observacion||"",h=p=>Number(p||0).toFixed(0),f=l.map(p=>`
      <tr>
        <td class="qty">${Number(p.cantidad||0)}</td>
        <td class="prod">${p.producto_nombre||""}</td>
        <td class="num">${h(p.subtotal)}</td>
      </tr>
    `).join(""),g=o.map(p=>`
      <tr>
        <td class="qty">${Number(p.cantidad||0)}</td>
        <td class="mat">${p.inventario?.nombre||"material"}</td>
        <td class="num">${h(p.efectivo??p.fisico_recibido)}</td>
        <td class="tipo">${String(p.tipo||"-").toUpperCase()}</td>
      </tr>
    `).join(""),_=g?`
        <table class="material-table">
          <thead>
            <tr>
              <th>CANT</th>
              <th>MATERIAL</th>
              <th>MONTO</th>
              <th>TIPO</th>
            </tr>
          </thead>
          <tbody>${g}</tbody>
        </table>`:"",M=`
      <style>
        .despacho-ticket {
          width: 300px;
          font-family: "Times New Roman", serif;
          color: #111;
          font-size: 11px;
          line-height: 1.1;
        }
        .despacho-ticket .top-title {
          display: flex;
          justify-content: flex-end;
          gap: 28px;
          font-size: 12px;
          margin-bottom: 4px;
        }
        .despacho-ticket .row-line {
          font-size: 11px;
        }
        .despacho-ticket .dash {
          border: 0;
          border-top: 1px dashed #222;
          margin: 4px 0;
        }
        .despacho-ticket .tear {
          text-align: center;
          letter-spacing: 2px;
          margin: 6px 0 4px;
          font-weight: bold;
          overflow: hidden;
          white-space: nowrap;
        }
        .despacho-ticket table {
          width: 100%;
          border-collapse: collapse;
        }
        .despacho-ticket th {
          font-size: 11px;
          font-weight: bold;
          text-align: left;
          padding: 0;
        }
        .despacho-ticket td {
          font-size: 11px;
          padding: 0;
          vertical-align: top;
        }
        .despacho-ticket .qty {
          width: 28px;
        }
        .despacho-ticket .prod {
          width: 180px;
          text-align: center;
          text-transform: lowercase;
        }
        .despacho-ticket .num {
          width: 44px;
          text-align: center;
        }
        .despacho-ticket .mat {
          width: 118px;
          text-align: center;
          word-break: break-word;
          text-transform: lowercase;
        }
        .despacho-ticket .tipo {
          width: 74px;
          text-align: right;
        }
        .despacho-ticket .section-title {
          text-align: center;
          font-size: 11px;
          margin-bottom: 2px;
        }
        .despacho-ticket .copy-title {
          text-align: center;
          font-size: 12px;
          margin-bottom: 2px;
        }
        .despacho-ticket .strong {
          font-size: 12px;
          font-weight: bold;
        }
        .despacho-ticket .firma {
          margin-top: 40px;
          text-align: center;
          font-size: 12px;
          font-weight: bold;
        }
        .despacho-ticket .terms {
          margin-top: 6px;
          font-size: 11px;
          font-weight: bold;
        }
      </style>
      <div class="despacho-ticket">
        <div class="top-title">
          <span>Ficha de Despacho</span>
          <span>${a}</span>
        </div>
        <div class="row-line">Nro :${t.id}</div>
        ${t.tipo_venta==="local"?`<div><b>Local:</b> ${t.cliente_direccion||"-"}</div>`:""}
        <div class="row-line">Nombre: ${c}</div>
        <div class="row-line">Usuario: ${d}</div>
        <hr class="dash">
        <table>
          <thead>
            <tr>
              <th>Cant</th>
              <th style="text-align:center;">Prod</th>
              <th style="text-align:center;">Subt</th>
            </tr>
          </thead>
          <tbody>${f||'<tr><td colspan="3">Sin detalle</td></tr>'}</tbody>
        </table>
        <div class="strong">TOTAL: ${h(t.total)} Bs</div>
        ${Number(t.monto_efectivo||0)>0?`<div class="row-line">Efectivo: ${h(t.monto_efectivo)} Bs</div>`:""}
        ${Number(t.monto_qr||0)>0?`<div class="row-line">QR: ${h(t.monto_qr)} Bs</div>`:""}
        ${Number(t.saldo_pendiente||0)>0?`<div class="row-line">Saldo: ${h(t.saldo_pendiente)} Bs</div>`:""}
        <div>Observacion: ${u}</div>
        ${_}
        ${g?`
          <div class="tear">########################</div>
          <div class="section-title">Detalle de Prestamo o Venta de material</div>
          <div class="strong">Cliente - ${c}</div>
          ${_}
          <div class="tear">########################</div>
          <div class="copy-title">copia para archivo</div>
          <div class="strong">Cliente - ${c}</div>
          <div class="strong">Fecha: ${a.split("/").reverse().join("-")}</div>
          <div class="strong">Usuario: ${d}</div>
          ${_}
          <div class="firma">FIRMA</div>
          <div class="terms">* Acepto todas las condiciones y terminos de prestamo de envases</div>
        `:""}
      </div>
    `;this.printTicketHtml(M)}static hojaRuta(t){const r=new Date(t.created_at||Date.now()),n=String(r.getDate()).padStart(2,"0"),i=String(r.getMonth()+1).padStart(2,"0"),s=r.getFullYear(),a=t.hoja_hora||`${String(r.getHours()).padStart(2,"0")}:${String(r.getMinutes()).padStart(2,"0")}:${String(r.getSeconds()).padStart(2,"0")}`,l=`${n}/${i}/${s}`,o=t.hoja_fecha_entrega||t.fecha||t.fecha_venta||null,d=o?String(o).slice(0,10).split("-").reverse().join("/"):l,c=t.detalles||[],u=t.prestamos||[],h=c.map(D=>`
      <tr>
        <td>${Number(D.cantidad||0)}</td>
        <td>${D.producto_nombre||""}</td>
      </tr>
    `).join(""),f=u.map(D=>`
      <tr>
        <td>${Number(D.cantidad||0)}</td>
        <td>${D.inventario?.nombre||"material"}</td>
        <td>${D.tipo}</td>
      </tr>
    `).join(""),g=(t.pagos||[]).find(D=>D.estado==="PAGADO")?.metodo||"-",_=t.hoja_cuenta??t.total_pagado??0,M=t.hoja_saldo??t.saldo_pendiente??t.total??0,p=`
      <div style="width:300px;font-family: 'Times New Roman', serif; font-size:14px;">
        <div style="text-align:center;">
          <div style="font-size:16px;font-weight:bold;">Hoja de Ruta</div>
          <div>Nro ${t.id}</div>
        </div>
        <table style="width:100%;margin-top:6px;">
          <tr><td>Fecha:</td><td><b>${l}</b></td></tr>
          <tr><td>Fecha entrega:</td><td><b>${d}</b></td></tr>
          <tr><td>Turno:</td><td><b>${t.hoja_turno||"-"}</b></td></tr>
        </table>
        <hr>
        <table style="width:100%;border-collapse:collapse;">
          <thead><tr><th style="text-align:left">CANTIDAD</th><th style="text-align:left">PRODUCTO</th></tr></thead>
          <tbody>${h||'<tr><td colspan="2">Sin detalle</td></tr>'}</tbody>
        </table>
        ${u.length?`
        <hr>
        <table style="width:100%;border-collapse:collapse;">
          <thead><tr><th style="text-align:left">CANTIDAD</th><th style="text-align:left">MATERIAL</th><th style="text-align:left">TIPO</th></tr></thead>
          <tbody>${f}</tbody>
        </table>`:""}
        <div style="margin-top:8px;"><b>Nombre:</b> ${t.cliente_nombre||"-"}</div>
        <div><b>Tel 1:</b> ${t.hoja_telefono_1||t.cliente_telefono||"-"}</div>
        <div><b>Tel 2:</b> ${t.hoja_telefono_2||"-"}</div>
        <div><b>Direccion:</b> ${t.hoja_direccion||t.cliente_direccion||"-"} &nbsp; <b>Turno:</b> ${t.hoja_turno||"-"}</div>
        <div><b>Hora:</b> ${a}</div>
        <div><b>Envases:</b> ${t.hoja_envases||"-"}</div>
        <hr>
        <div><b>Observacion:</b> ${t.hoja_observaciones||t.observacion||""}</div>
        <div><b>Total:</b> ${Number(t.total||0).toFixed(2)}</div>
        <div><b>A cuenta:</b> ${Number(_||0).toFixed(2)}</div>
        <div><b>Saldo:</b> ${Number(M||0).toFixed(2)}</div>
        <div><b>Metodo:</b> ${g}</div>
        <div><b>Usuario:</b> ${t.user?.name||t.user?.username||"-"}</div>
      </div>
    `;this.printTicketHtml(p)}static hojaRutaMapa(t){const r=[-17.9645186,-67.124877];let n=Number(t.hoja_lat??t.cliente_lat),i=Number(t.hoja_lng??t.cliente_lng);const s=!Number.isFinite(n)||!Number.isFinite(i);s&&([n,i]=r);const l=`https://staticmap.openstreetmap.de/staticmap.php?center=${n},${i}&zoom=${s?13:16}&size=380x380&maptype=mapnik&markers=${n},${i},red-pushpin`,o=`
      <div style="width:300px;font-family: 'Times New Roman', serif; font-size:14px;">
        <div style="text-align:center;">
          <div style="font-size:16px;font-weight:bold;">Hoja de Ruta</div>
          <div>Nro ${t.id} - Mapa</div>
        </div>
        <hr>
        <div style="margin-bottom:6px;"><b>Nombre:</b> ${t.cliente_nombre||"-"}</div>
        <div style="margin-bottom:6px;"><b>Direccion:</b> ${t.hoja_direccion||t.cliente_direccion||"-"}</div>
        <img src="${l}" style="width:100%;border-radius:6px;" crossorigin="anonymous" />
        <div style="margin-top:6px;font-size:12px;">
          <b>Coordenadas:</b> ${n.toFixed(6)}, ${i.toFixed(6)}${s?" (centro de Oruro por defecto)":""}
        </div>
      </div>
    `;this.printTicketHtmlConImagenes(o)}static prestamoMaterialTicket(t){const r=l=>(l??"").toString(),n=l=>Number(l||0).toFixed(2),i=l=>{if(!l)return"-";const o=r(l).split("T")[0].split("-");return o.length===3?`${o[2]}/${o[1]}/${o[0]}`:r(l)},a=`
      <div style="width:300px;font-family:'Times New Roman',serif;font-size:14px;line-height:1.2;color:#111;">
        <div style="text-align:center;font-size:18px;font-weight:bold;">${t.tipo==="venta"?"Venta Material":"Devolución de garantía"}</div>
        <div style="text-align:center;">Nro: ${t.id||""}</div>
        <hr style="border:0;border-top:1px dashed #222;margin:8px 0;">
        <div><b>Fecha:</b> ${i(t.fecha)}</div>
        <div><b>Cliente:</b> ${r(t.cliente?.nombre||"-")}</div>
        <div><b>Material:</b> ${r(t.inventario?.nombre||"-")}</div>
        <div><b>Estado:</b> ${r(t.estado||"-")}</div>
        <div><b>Cantidad:</b> ${Number(t.cantidad||0)}</div>
        <div><b>Pendiente:</b> ${Number(t.cantidad_actual||0)}</div>
        <div><b>Monto recibido:</b> ${n(t.fisico_recibido||t.efectivo||0)} Bs</div>
        <div><b>Monto pendiente:</b> ${n(t.monto_pendiente??t.efectivo_actual??0)} Bs</div>
        <div><b>Observacion:</b> ${r(t.observacion||"")}</div>
        <br><br><br>
        <div style="text-align:center;font-weight:bold;">FIRMA</div>
        ${t.tipo!=="venta"?'<div style="margin-top:10px;font-size:16px;font-weight:bold;">* Acepto todas las condiciones y terminos de prestamo de envases</div>':""}
      </div>
    `;this.printTicketHtml(a)}static prestamoHistorialTicket(t){const r=l=>(l??"").toString(),n=l=>Number(l||0).toFixed(2),i=l=>{if(!l)return"-";const o=r(l).split("T")[0].split("-");return o.length===3?`${o[2]}/${o[1]}/${o[0]}`:r(l)},s=(t.retornos||[]).map(l=>`
      <tr>
        <td>${i(l.fecha)}</td>
        <td style="text-align:right;">${Number(l.cantidad||0)}</td>
        <td style="text-align:right;">${n(l.efectivo)}</td>
      </tr>
      ${l.observacion?`<tr><td colspan="3" style="font-size:12px;">${r(l.observacion)}</td></tr>`:""}
    `).join(""),a=`
      <div style="width:300px;font-family:'Times New Roman',serif;font-size:14px;line-height:1.2;color:#111;">
        <div style="text-align:center;font-size:18px;font-weight:bold;">Historial Retornos</div>
        <div style="text-align:center;">Prestamo #${t.id||""}</div>
        <hr style="border:0;border-top:1px dashed #222;margin:8px 0;">
        <div><b>Cliente:</b> ${r(t.cliente?.nombre||"-")}</div>
        <div><b>Material:</b> ${r(t.inventario?.nombre||"-")}</div>
        <div><b>Estado:</b> ${r(t.estado||"-")}</div>
        <div><b>Cantidad:</b> ${Number(t.cantidad||0)}</div>
        <div><b>Retornado:</b> ${Number(t.retornado_cantidad||0)}</div>
        <div><b>Pendiente:</b> ${Number(t.cantidad_actual||0)}</div>
        <hr style="border:0;border-top:1px dashed #222;margin:8px 0;">
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr><th style="text-align:left;">Fecha</th><th style="text-align:right;">Cant</th><th style="text-align:right;">Monto</th></tr>
          </thead>
          <tbody>${s||'<tr><td colspan="3">Sin retornos</td></tr>'}</tbody>
        </table>
      </div>
    `;this.printTicketHtml(a)}static reportePrestamosTicket(t=[],r={}){const n=f=>(f??"").toString(),i=f=>Number(f||0).toFixed(2),s=f=>{if(!f)return"-";const g=n(f).split("T")[0].split("-");return g.length===3?`${g[2]}/${g[1]}/${g[0]}`:n(f)},a=r.dateFrom===r.dateTo?s(r.dateFrom):`${s(r.dateFrom)} - ${s(r.dateTo)}`,l=t.reduce((f,g)=>f+Number(g.cantidad||0),0),o=t.reduce((f,g)=>f+Number(g.cantidad_actual||0),0),d=t.reduce((f,g)=>f+Number(g.fisico_recibido||g.efectivo||0),0),c=t.reduce((f,g)=>f+Number(g.monto_pendiente??g.efectivo_actual??0),0),u=t.map(f=>`
      <tr>
        <td style="width:26px;">${f.id||""}</td>
        <td>
          <div style="font-weight:bold;">${n(f.cliente?.nombre||"-")}</div>
          <div>${n(f.inventario?.nombre||"-")}</div>
          <div>${s(f.fecha)} ${n(f.estado||"")}</div>
        </td>
        <td style="text-align:right;width:40px;">${Number(f.cantidad||0)}</td>
        <td style="text-align:right;width:54px;">${i(f.fisico_recibido||f.efectivo||0)}</td>
      </tr>
    `).join(""),h=`
      <div style="width:300px;font-family:'Times New Roman',serif;font-size:13px;line-height:1.15;color:#111;">
        <div style="text-align:center;font-size:18px;font-weight:bold;">${r.titulo||"Reporte Prestamos"}</div>
        <div><b>Tipo:</b> ${n(r.tipoVenta||"").toUpperCase()||"TODOS"}</div>
        <div><b>Periodo:</b> ${a}</div>
        <div><b>Filtro:</b> ${n(r.filtro||"Todos")}</div>
        <div><b>Registros:</b> ${t.length}</div>
        <hr style="border:0;border-top:1px dashed #222;margin:7px 0;">
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr><th>ID</th><th>CLIENTE / MATERIAL</th><th style="text-align:right;">CANT</th><th style="text-align:right;">MONTO</th></tr>
          </thead>
          <tbody>${u||'<tr><td colspan="4">Sin registros</td></tr>'}</tbody>
        </table>
        <hr style="border:0;border-top:1px dashed #222;margin:7px 0;">
        <div style="display:flex;justify-content:space-between;font-weight:bold;"><span>Cantidad</span><span>${l}</span></div>
        <div style="display:flex;justify-content:space-between;font-weight:bold;"><span>Cant. pendiente</span><span>${o}</span></div>
        <div style="display:flex;justify-content:space-between;font-weight:bold;"><span>Monto</span><span>${i(d)} Bs</span></div>
        <div style="display:flex;justify-content:space-between;font-weight:bold;"><span>Pendiente</span><span>${i(c)} Bs</span></div>
      </div>
    `;this.printTicketHtml(h)}static deudaTicket(t){const r=c=>(c??"").toString(),n=c=>Number(c||0).toFixed(2),i=c=>{if(!c)return"-";const u=r(c).split("T")[0].split("-");return u.length===3?`${u[2]}/${u[1]}/${u[0]}`:r(c)},s=t.pagos||[],a=t.total_pagado??s.filter(c=>c.estado==="PAGADO").reduce((c,u)=>c+Number(u.monto||0),0),l=t.saldo_pendiente??s.filter(c=>c.estado==="PENDIENTE").reduce((c,u)=>c+Number(u.monto||0),0),o=s.map(c=>`
      <tr>
        <td>${c.nro_cuota??"-"}</td>
        <td style="text-align:right;">${n(c.monto)}</td>
        <td>${r(c.metodo||"-")}</td>
        <td style="font-size:10px;">${r(c.estado||"-")}</td>
        <td style="font-size:10px;">${i(c.fecha_pago)}</td>
      </tr>
    `).join(""),d=`
      <div style="width:300px;font-family:'Times New Roman',serif;font-size:12px;line-height:1.2;color:#111;">
        <div style="text-align:center;font-size:15px;font-weight:bold;">Comprobante de Pago</div>
        <div style="text-align:center;">Venta #${t.id||""}</div>
        <hr style="border:0;border-top:1px dashed #222;margin:6px 0;">
        <div><b>Fecha:</b> ${i(t.fecha_venta||t.created_at)}</div>
        <div><b>Cliente:</b> ${r(t.cliente_nombre||"-")}</div>
        <div><b>Telefono:</b> ${r(t.cliente_telefono||"-")}</div>
        <hr style="border:0;border-top:1px dashed #222;margin:6px 0;">
        <table style="width:100%;border-collapse:collapse;font-size:11px;">
          <thead>
            <tr style="font-weight:bold;">
              <th style="text-align:left;">Cta</th>
              <th style="text-align:right;">Monto</th>
              <th>Metodo</th>
              <th>Estado</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>${o||'<tr><td colspan="5">Sin pagos</td></tr>'}</tbody>
        </table>
        <hr style="border:0;border-top:1px dashed #222;margin:6px 0;">
        <div style="display:flex;justify-content:space-between;"><span><b>Total:</b></span><span>${n(t.total)} Bs</span></div>
        <div style="display:flex;justify-content:space-between;"><span><b>Pagado:</b></span><span>${n(a)} Bs</span></div>
        <div style="display:flex;justify-content:space-between;font-weight:bold;${Number(l)>0?"color:#b45309;":"color:#166534;"}">
          <span>Saldo pendiente:</span><span>${n(l)} Bs</span>
        </div>
      </div>
    `;this.printTicketHtml(d)}static movimientoCaja(t,r="Caja"){const n=new Date(t.created_at||Date.now()),i=String(n.getDate()).padStart(2,"0"),s=String(n.getMonth()+1).padStart(2,"0"),a=n.getFullYear(),l=String(n.getHours()).padStart(2,"0"),o=String(n.getMinutes()).padStart(2,"0"),d=String(n.getSeconds()).padStart(2,"0"),c=`
      <div style="width:300px;font-family: 'Times New Roman', serif; font-size:14px;">
        <div style="text-align:center;border-bottom:1px solid #222;padding-bottom:4px;margin-bottom:8px;">
          <div style="font-size:24px;font-weight:bold;">Comprobante de caja</div>
          <div style="font-size:16px">${i}/${s}/${a} ${l}:${o}:${d}</div>
        </div>
        <div><b>ID:</b> ${t.id}</div>
        <div><b>Caja:</b> ${r}</div>
        <div><b>Tipo:</b> ${t.tipo_movimiento||"-"}</div>
        <div><b>Origen:</b> ${t.tipo_venta||"-"}</div>
        <div><b>Usuario:</b> ${t.usuario||"-"}</div>
        <hr>
        <div style="font-size:18px;"><b>Monto: ${Number(t.monto_real||0).toFixed(2)} Bs</b></div>
        <div style="margin-top:8px;"><b>Observacion:</b> ${t.observacion||""}</div>
      </div>
    `;this.printTicketHtml(c)}static pagoPersonalMovimiento(t){const r=`
      <div style="width:300px;font-family: 'Times New Roman', serif; font-size:14px;">
        <div style="text-align:center;border-bottom:1px solid #222;padding-bottom:4px;margin-bottom:8px;">
          <div style="font-size:20px;font-weight:bold;">Comprobante Personal</div>
          <div>${t.mes||""} - ${t.tipo_registro||""}</div>
        </div>
        <div><b>Personal:</b> ${t.personal?.nombre||"-"}</div>
        <div><b>CI:</b> ${t.personal?.ci||"-"}</div>
        <div><b>Caja:</b> ${t.caja?.nombre||"-"}</div>
        <div><b>Tipo:</b> ${t.tipo_registro||"-"}</div>
        <div><b>Monto:</b> ${Number(t.monto_pagado||0).toFixed(2)} Bs</div>
        <div><b>Obs:</b> ${t.observacion||""}</div>
      </div>
    `;this.printTicketHtml(r)}}export{Kl as I};
