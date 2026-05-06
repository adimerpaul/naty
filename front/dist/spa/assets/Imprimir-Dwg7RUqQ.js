import{g as Dn}from"./_commonjsHelpers-CqkleIqs.js";import{v as ie}from"./index-_zSP0Zu2.js";var Le={},Pt,Br;function Ss(){return Br||(Br=1,Pt=function(){return typeof Promise=="function"&&Promise.prototype&&Promise.prototype.then}),Pt}var Lt={},Te={},Wr;function Ae(){if(Wr)return Te;Wr=1;let e;const t=[0,26,44,70,100,134,172,196,242,292,346,404,466,532,581,655,733,815,901,991,1085,1156,1258,1364,1474,1588,1706,1828,1921,2051,2185,2323,2465,2611,2761,2876,3034,3196,3362,3532,3706];return Te.getSymbolSize=function(n){if(!n)throw new Error('"version" cannot be null or undefined');if(n<1||n>40)throw new Error('"version" should be in range from 1 to 40');return n*4+17},Te.getSymbolTotalCodewords=function(n){return t[n]},Te.getBCHDigit=function(r){let n=0;for(;r!==0;)n++,r>>>=1;return n},Te.setToSJISFunction=function(n){if(typeof n!="function")throw new Error('"toSJISFunc" is not a valid function.');e=n},Te.isKanjiModeEnabled=function(){return typeof e<"u"},Te.toSJIS=function(n){return e(n)},Te}var Yt={},zr;function yr(){return zr||(zr=1,(function(e){e.L={bit:1},e.M={bit:0},e.Q={bit:3},e.H={bit:2};function t(r){if(typeof r!="string")throw new Error("Param is not a string");switch(r.toLowerCase()){case"l":case"low":return e.L;case"m":case"medium":return e.M;case"q":case"quartile":return e.Q;case"h":case"high":return e.H;default:throw new Error("Unknown EC Level: "+r)}}e.isValid=function(n){return n&&typeof n.bit<"u"&&n.bit>=0&&n.bit<4},e.from=function(n,s){if(e.isValid(n))return n;try{return t(n)}catch{return s}}})(Yt)),Yt}var It,Hr;function Ms(){if(Hr)return It;Hr=1;function e(){this.buffer=[],this.length=0}return e.prototype={get:function(t){const r=Math.floor(t/8);return(this.buffer[r]>>>7-t%8&1)===1},put:function(t,r){for(let n=0;n<r;n++)this.putBit((t>>>r-n-1&1)===1)},getLengthInBits:function(){return this.length},putBit:function(t){const r=Math.floor(this.length/8);this.buffer.length<=r&&this.buffer.push(0),t&&(this.buffer[r]|=128>>>this.length%8),this.length++}},It=e,It}var $t,Vr;function Ts(){if(Vr)return $t;Vr=1;function e(t){if(!t||t<1)throw new Error("BitMatrix size must be defined and greater than 0");this.size=t,this.data=new Uint8Array(t*t),this.reservedBit=new Uint8Array(t*t)}return e.prototype.set=function(t,r,n,s){const i=t*this.size+r;this.data[i]=n,s&&(this.reservedBit[i]=!0)},e.prototype.get=function(t,r){return this.data[t*this.size+r]},e.prototype.xor=function(t,r,n){this.data[t*this.size+r]^=n},e.prototype.isReserved=function(t,r){return this.reservedBit[t*this.size+r]},$t=e,$t}var Ut={},jr;function Ds(){return jr||(jr=1,(function(e){const t=Ae().getSymbolSize;e.getRowColCoords=function(n){if(n===1)return[];const s=Math.floor(n/7)+2,i=t(n),a=i===145?26:Math.ceil((i-13)/(2*s-2))*2,l=[i-7];for(let o=1;o<s-1;o++)l[o]=l[o-1]-a;return l.push(6),l.reverse()},e.getPositions=function(n){const s=[],i=e.getRowColCoords(n),a=i.length;for(let l=0;l<a;l++)for(let o=0;o<a;o++)l===0&&o===0||l===0&&o===a-1||l===a-1&&o===0||s.push([i[l],i[o]]);return s}})(Ut)),Ut}var Bt={},qr;function ks(){if(qr)return Bt;qr=1;const e=Ae().getSymbolSize,t=7;return Bt.getPositions=function(n){const s=e(n);return[[0,0],[s-t,0],[0,s-t]]},Bt}var Wt={},Gr;function Cs(){return Gr||(Gr=1,(function(e){e.Patterns={PATTERN000:0,PATTERN001:1,PATTERN010:2,PATTERN011:3,PATTERN100:4,PATTERN101:5,PATTERN110:6,PATTERN111:7};const t={N1:3,N2:3,N3:40,N4:10};e.isValid=function(s){return s!=null&&s!==""&&!isNaN(s)&&s>=0&&s<=7},e.from=function(s){return e.isValid(s)?parseInt(s,10):void 0},e.getPenaltyN1=function(s){const i=s.size;let a=0,l=0,o=0,d=null,c=null;for(let u=0;u<i;u++){l=o=0,d=c=null;for(let h=0;h<i;h++){let f=s.get(u,h);f===d?l++:(l>=5&&(a+=t.N1+(l-5)),d=f,l=1),f=s.get(h,u),f===c?o++:(o>=5&&(a+=t.N1+(o-5)),c=f,o=1)}l>=5&&(a+=t.N1+(l-5)),o>=5&&(a+=t.N1+(o-5))}return a},e.getPenaltyN2=function(s){const i=s.size;let a=0;for(let l=0;l<i-1;l++)for(let o=0;o<i-1;o++){const d=s.get(l,o)+s.get(l,o+1)+s.get(l+1,o)+s.get(l+1,o+1);(d===4||d===0)&&a++}return a*t.N2},e.getPenaltyN3=function(s){const i=s.size;let a=0,l=0,o=0;for(let d=0;d<i;d++){l=o=0;for(let c=0;c<i;c++)l=l<<1&2047|s.get(d,c),c>=10&&(l===1488||l===93)&&a++,o=o<<1&2047|s.get(c,d),c>=10&&(o===1488||o===93)&&a++}return a*t.N3},e.getPenaltyN4=function(s){let i=0;const a=s.data.length;for(let o=0;o<a;o++)i+=s.data[o];return Math.abs(Math.ceil(i*100/a/5)-10)*t.N4};function r(n,s,i){switch(n){case e.Patterns.PATTERN000:return(s+i)%2===0;case e.Patterns.PATTERN001:return s%2===0;case e.Patterns.PATTERN010:return i%3===0;case e.Patterns.PATTERN011:return(s+i)%3===0;case e.Patterns.PATTERN100:return(Math.floor(s/2)+Math.floor(i/3))%2===0;case e.Patterns.PATTERN101:return s*i%2+s*i%3===0;case e.Patterns.PATTERN110:return(s*i%2+s*i%3)%2===0;case e.Patterns.PATTERN111:return(s*i%3+(s+i)%2)%2===0;default:throw new Error("bad maskPattern:"+n)}}e.applyMask=function(s,i){const a=i.size;for(let l=0;l<a;l++)for(let o=0;o<a;o++)i.isReserved(o,l)||i.xor(o,l,r(s,o,l))},e.getBestMask=function(s,i){const a=Object.keys(e.Patterns).length;let l=0,o=1/0;for(let d=0;d<a;d++){i(d),e.applyMask(d,s);const c=e.getPenaltyN1(s)+e.getPenaltyN2(s)+e.getPenaltyN3(s)+e.getPenaltyN4(s);e.applyMask(d,s),c<o&&(o=c,l=d)}return l}})(Wt)),Wt}var dt={},Zr;function kn(){if(Zr)return dt;Zr=1;const e=yr(),t=[1,1,1,1,1,1,1,1,1,1,2,2,1,2,2,4,1,2,4,4,2,4,4,4,2,4,6,5,2,4,6,6,2,5,8,8,4,5,8,8,4,5,8,11,4,8,10,11,4,9,12,16,4,9,16,16,6,10,12,18,6,10,17,16,6,11,16,19,6,13,18,21,7,14,21,25,8,16,20,25,8,17,23,25,9,17,23,34,9,18,25,30,10,20,27,32,12,21,29,35,12,23,34,37,12,25,34,40,13,26,35,42,14,28,38,45,15,29,40,48,16,31,43,51,17,33,45,54,18,35,48,57,19,37,51,60,19,38,53,63,20,40,56,66,21,43,59,70,22,45,62,74,24,47,65,77,25,49,68,81],r=[7,10,13,17,10,16,22,28,15,26,36,44,20,36,52,64,26,48,72,88,36,64,96,112,40,72,108,130,48,88,132,156,60,110,160,192,72,130,192,224,80,150,224,264,96,176,260,308,104,198,288,352,120,216,320,384,132,240,360,432,144,280,408,480,168,308,448,532,180,338,504,588,196,364,546,650,224,416,600,700,224,442,644,750,252,476,690,816,270,504,750,900,300,560,810,960,312,588,870,1050,336,644,952,1110,360,700,1020,1200,390,728,1050,1260,420,784,1140,1350,450,812,1200,1440,480,868,1290,1530,510,924,1350,1620,540,980,1440,1710,570,1036,1530,1800,570,1064,1590,1890,600,1120,1680,1980,630,1204,1770,2100,660,1260,1860,2220,720,1316,1950,2310,750,1372,2040,2430];return dt.getBlocksCount=function(s,i){switch(i){case e.L:return t[(s-1)*4+0];case e.M:return t[(s-1)*4+1];case e.Q:return t[(s-1)*4+2];case e.H:return t[(s-1)*4+3];default:return}},dt.getTotalCodewordsCount=function(s,i){switch(i){case e.L:return r[(s-1)*4+0];case e.M:return r[(s-1)*4+1];case e.Q:return r[(s-1)*4+2];case e.H:return r[(s-1)*4+3];default:return}},dt}var zt={},Ge={},Jr;function Es(){if(Jr)return Ge;Jr=1;const e=new Uint8Array(512),t=new Uint8Array(256);return(function(){let n=1;for(let s=0;s<255;s++)e[s]=n,t[n]=s,n<<=1,n&256&&(n^=285);for(let s=255;s<512;s++)e[s]=e[s-255]})(),Ge.log=function(n){if(n<1)throw new Error("log("+n+")");return t[n]},Ge.exp=function(n){return e[n]},Ge.mul=function(n,s){return n===0||s===0?0:e[t[n]+t[s]]},Ge}var Kr;function xs(){return Kr||(Kr=1,(function(e){const t=Es();e.mul=function(n,s){const i=new Uint8Array(n.length+s.length-1);for(let a=0;a<n.length;a++)for(let l=0;l<s.length;l++)i[a+l]^=t.mul(n[a],s[l]);return i},e.mod=function(n,s){let i=new Uint8Array(n);for(;i.length-s.length>=0;){const a=i[0];for(let o=0;o<s.length;o++)i[o]^=t.mul(s[o],a);let l=0;for(;l<i.length&&i[l]===0;)l++;i=i.slice(l)}return i},e.generateECPolynomial=function(n){let s=new Uint8Array([1]);for(let i=0;i<n;i++)s=e.mul(s,new Uint8Array([1,t.exp(i)]));return s}})(zt)),zt}var Ht,Qr;function Ns(){if(Qr)return Ht;Qr=1;const e=xs();function t(r){this.genPoly=void 0,this.degree=r,this.degree&&this.initialize(this.degree)}return t.prototype.initialize=function(n){this.degree=n,this.genPoly=e.generateECPolynomial(this.degree)},t.prototype.encode=function(n){if(!this.genPoly)throw new Error("Encoder not initialized");const s=new Uint8Array(n.length+this.degree);s.set(n);const i=e.mod(s,this.genPoly),a=this.degree-i.length;if(a>0){const l=new Uint8Array(this.degree);return l.set(i,a),l}return i},Ht=t,Ht}var Vt={},jt={},qt={},Xr;function Cn(){return Xr||(Xr=1,qt.isValid=function(t){return!isNaN(t)&&t>=1&&t<=40}),qt}var ae={},en;function En(){if(en)return ae;en=1;const e="[0-9]+",t="[A-Z $%*+\\-./:]+";let r="(?:[u3000-u303F]|[u3040-u309F]|[u30A0-u30FF]|[uFF00-uFFEF]|[u4E00-u9FAF]|[u2605-u2606]|[u2190-u2195]|u203B|[u2010u2015u2018u2019u2025u2026u201Cu201Du2225u2260]|[u0391-u0451]|[u00A7u00A8u00B1u00B4u00D7u00F7])+";r=r.replace(/u/g,"\\u");const n="(?:(?![A-Z0-9 $%*+\\-./:]|"+r+`)(?:.|[\r
]))+`;ae.KANJI=new RegExp(r,"g"),ae.BYTE_KANJI=new RegExp("[^A-Z0-9 $%*+\\-./:]+","g"),ae.BYTE=new RegExp(n,"g"),ae.NUMERIC=new RegExp(e,"g"),ae.ALPHANUMERIC=new RegExp(t,"g");const s=new RegExp("^"+r+"$"),i=new RegExp("^"+e+"$"),a=new RegExp("^[A-Z0-9 $%*+\\-./:]+$");return ae.testKanji=function(o){return s.test(o)},ae.testNumeric=function(o){return i.test(o)},ae.testAlphanumeric=function(o){return a.test(o)},ae}var tn;function Fe(){return tn||(tn=1,(function(e){const t=Cn(),r=En();e.NUMERIC={id:"Numeric",bit:1,ccBits:[10,12,14]},e.ALPHANUMERIC={id:"Alphanumeric",bit:2,ccBits:[9,11,13]},e.BYTE={id:"Byte",bit:4,ccBits:[8,16,16]},e.KANJI={id:"Kanji",bit:8,ccBits:[8,10,12]},e.MIXED={bit:-1},e.getCharCountIndicator=function(i,a){if(!i.ccBits)throw new Error("Invalid mode: "+i);if(!t.isValid(a))throw new Error("Invalid version: "+a);return a>=1&&a<10?i.ccBits[0]:a<27?i.ccBits[1]:i.ccBits[2]},e.getBestModeForData=function(i){return r.testNumeric(i)?e.NUMERIC:r.testAlphanumeric(i)?e.ALPHANUMERIC:r.testKanji(i)?e.KANJI:e.BYTE},e.toString=function(i){if(i&&i.id)return i.id;throw new Error("Invalid mode")},e.isValid=function(i){return i&&i.bit&&i.ccBits};function n(s){if(typeof s!="string")throw new Error("Param is not a string");switch(s.toLowerCase()){case"numeric":return e.NUMERIC;case"alphanumeric":return e.ALPHANUMERIC;case"kanji":return e.KANJI;case"byte":return e.BYTE;default:throw new Error("Unknown mode: "+s)}}e.from=function(i,a){if(e.isValid(i))return i;try{return n(i)}catch{return a}}})(jt)),jt}var rn;function Os(){return rn||(rn=1,(function(e){const t=Ae(),r=kn(),n=yr(),s=Fe(),i=Cn(),a=7973,l=t.getBCHDigit(a);function o(h,f,p){for(let b=1;b<=40;b++)if(f<=e.getCapacity(b,p,h))return b}function d(h,f){return s.getCharCountIndicator(h,f)+4}function c(h,f){let p=0;return h.forEach(function(b){const M=d(b.mode,f);p+=M+b.getBitsLength()}),p}function u(h,f){for(let p=1;p<=40;p++)if(c(h,p)<=e.getCapacity(p,f,s.MIXED))return p}e.from=function(f,p){return i.isValid(f)?parseInt(f,10):p},e.getCapacity=function(f,p,b){if(!i.isValid(f))throw new Error("Invalid QR Code version");typeof b>"u"&&(b=s.BYTE);const M=t.getSymbolTotalCodewords(f),g=r.getTotalCodewordsCount(f,p),k=(M-g)*8;if(b===s.MIXED)return k;const E=k-d(b,f);switch(b){case s.NUMERIC:return Math.floor(E/10*3);case s.ALPHANUMERIC:return Math.floor(E/11*2);case s.KANJI:return Math.floor(E/13);case s.BYTE:default:return Math.floor(E/8)}},e.getBestVersionForData=function(f,p){let b;const M=n.from(p,n.M);if(Array.isArray(f)){if(f.length>1)return u(f,M);if(f.length===0)return 1;b=f[0]}else b=f;return o(b.mode,b.getLength(),M)},e.getEncodedBits=function(f){if(!i.isValid(f)||f<7)throw new Error("Invalid QR Code version");let p=f<<12;for(;t.getBCHDigit(p)-l>=0;)p^=a<<t.getBCHDigit(p)-l;return f<<12|p}})(Vt)),Vt}var Gt={},nn;function As(){if(nn)return Gt;nn=1;const e=Ae(),t=1335,r=21522,n=e.getBCHDigit(t);return Gt.getEncodedBits=function(i,a){const l=i.bit<<3|a;let o=l<<10;for(;e.getBCHDigit(o)-n>=0;)o^=t<<e.getBCHDigit(o)-n;return(l<<10|o)^r},Gt}var Zt={},Jt,sn;function Fs(){if(sn)return Jt;sn=1;const e=Fe();function t(r){this.mode=e.NUMERIC,this.data=r.toString()}return t.getBitsLength=function(n){return 10*Math.floor(n/3)+(n%3?n%3*3+1:0)},t.prototype.getLength=function(){return this.data.length},t.prototype.getBitsLength=function(){return t.getBitsLength(this.data.length)},t.prototype.write=function(n){let s,i,a;for(s=0;s+3<=this.data.length;s+=3)i=this.data.substr(s,3),a=parseInt(i,10),n.put(a,10);const l=this.data.length-s;l>0&&(i=this.data.substr(s),a=parseInt(i,10),n.put(a,l*3+1))},Jt=t,Jt}var Kt,an;function Rs(){if(an)return Kt;an=1;const e=Fe(),t=["0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"," ","$","%","*","+","-",".","/",":"];function r(n){this.mode=e.ALPHANUMERIC,this.data=n}return r.getBitsLength=function(s){return 11*Math.floor(s/2)+6*(s%2)},r.prototype.getLength=function(){return this.data.length},r.prototype.getBitsLength=function(){return r.getBitsLength(this.data.length)},r.prototype.write=function(s){let i;for(i=0;i+2<=this.data.length;i+=2){let a=t.indexOf(this.data[i])*45;a+=t.indexOf(this.data[i+1]),s.put(a,11)}this.data.length%2&&s.put(t.indexOf(this.data[i]),6)},Kt=r,Kt}var Qt,on;function Ps(){if(on)return Qt;on=1;const e=Fe();function t(r){this.mode=e.BYTE,typeof r=="string"?this.data=new TextEncoder().encode(r):this.data=new Uint8Array(r)}return t.getBitsLength=function(n){return n*8},t.prototype.getLength=function(){return this.data.length},t.prototype.getBitsLength=function(){return t.getBitsLength(this.data.length)},t.prototype.write=function(r){for(let n=0,s=this.data.length;n<s;n++)r.put(this.data[n],8)},Qt=t,Qt}var Xt,ln;function Ls(){if(ln)return Xt;ln=1;const e=Fe(),t=Ae();function r(n){this.mode=e.KANJI,this.data=n}return r.getBitsLength=function(s){return s*13},r.prototype.getLength=function(){return this.data.length},r.prototype.getBitsLength=function(){return r.getBitsLength(this.data.length)},r.prototype.write=function(n){let s;for(s=0;s<this.data.length;s++){let i=t.toSJIS(this.data[s]);if(i>=33088&&i<=40956)i-=33088;else if(i>=57408&&i<=60351)i-=49472;else throw new Error("Invalid SJIS character: "+this.data[s]+`
Make sure your charset is UTF-8`);i=(i>>>8&255)*192+(i&255),n.put(i,13)}},Xt=r,Xt}var er={exports:{}},dn;function Ys(){return dn||(dn=1,(function(e){var t={single_source_shortest_paths:function(r,n,s){var i={},a={};a[n]=0;var l=t.PriorityQueue.make();l.push(n,0);for(var o,d,c,u,h,f,p,b,M;!l.empty();){o=l.pop(),d=o.value,u=o.cost,h=r[d]||{};for(c in h)h.hasOwnProperty(c)&&(f=h[c],p=u+f,b=a[c],M=typeof a[c]>"u",(M||b>p)&&(a[c]=p,l.push(c,p),i[c]=d))}if(typeof s<"u"&&typeof a[s]>"u"){var g=["Could not find a path from ",n," to ",s,"."].join("");throw new Error(g)}return i},extract_shortest_path_from_predecessor_list:function(r,n){for(var s=[],i=n;i;)s.push(i),r[i],i=r[i];return s.reverse(),s},find_path:function(r,n,s){var i=t.single_source_shortest_paths(r,n,s);return t.extract_shortest_path_from_predecessor_list(i,s)},PriorityQueue:{make:function(r){var n=t.PriorityQueue,s={},i;r=r||{};for(i in n)n.hasOwnProperty(i)&&(s[i]=n[i]);return s.queue=[],s.sorter=r.sorter||n.default_sorter,s},default_sorter:function(r,n){return r.cost-n.cost},push:function(r,n){var s={value:r,cost:n};this.queue.push(s),this.queue.sort(this.sorter)},pop:function(){return this.queue.shift()},empty:function(){return this.queue.length===0}}};e.exports=t})(er)),er.exports}var cn;function Is(){return cn||(cn=1,(function(e){const t=Fe(),r=Fs(),n=Rs(),s=Ps(),i=Ls(),a=En(),l=Ae(),o=Ys();function d(g){return unescape(encodeURIComponent(g)).length}function c(g,k,E){const _=[];let U;for(;(U=g.exec(E))!==null;)_.push({data:U[0],index:U.index,mode:k,length:U[0].length});return _}function u(g){const k=c(a.NUMERIC,t.NUMERIC,g),E=c(a.ALPHANUMERIC,t.ALPHANUMERIC,g);let _,U;return l.isKanjiModeEnabled()?(_=c(a.BYTE,t.BYTE,g),U=c(a.KANJI,t.KANJI,g)):(_=c(a.BYTE_KANJI,t.BYTE,g),U=[]),k.concat(E,_,U).sort(function(N,x){return N.index-x.index}).map(function(N){return{data:N.data,mode:N.mode,length:N.length}})}function h(g,k){switch(k){case t.NUMERIC:return r.getBitsLength(g);case t.ALPHANUMERIC:return n.getBitsLength(g);case t.KANJI:return i.getBitsLength(g);case t.BYTE:return s.getBitsLength(g)}}function f(g){return g.reduce(function(k,E){const _=k.length-1>=0?k[k.length-1]:null;return _&&_.mode===E.mode?(k[k.length-1].data+=E.data,k):(k.push(E),k)},[])}function p(g){const k=[];for(let E=0;E<g.length;E++){const _=g[E];switch(_.mode){case t.NUMERIC:k.push([_,{data:_.data,mode:t.ALPHANUMERIC,length:_.length},{data:_.data,mode:t.BYTE,length:_.length}]);break;case t.ALPHANUMERIC:k.push([_,{data:_.data,mode:t.BYTE,length:_.length}]);break;case t.KANJI:k.push([_,{data:_.data,mode:t.BYTE,length:d(_.data)}]);break;case t.BYTE:k.push([{data:_.data,mode:t.BYTE,length:d(_.data)}])}}return k}function b(g,k){const E={},_={start:{}};let U=["start"];for(let T=0;T<g.length;T++){const N=g[T],x=[];for(let S=0;S<N.length;S++){const O=N[S],C=""+T+S;x.push(C),E[C]={node:O,lastCount:0},_[C]={};for(let A=0;A<U.length;A++){const v=U[A];E[v]&&E[v].node.mode===O.mode?(_[v][C]=h(E[v].lastCount+O.length,O.mode)-h(E[v].lastCount,O.mode),E[v].lastCount+=O.length):(E[v]&&(E[v].lastCount=O.length),_[v][C]=h(O.length,O.mode)+4+t.getCharCountIndicator(O.mode,k))}}U=x}for(let T=0;T<U.length;T++)_[U[T]].end=0;return{map:_,table:E}}function M(g,k){let E;const _=t.getBestModeForData(g);if(E=t.from(k,_),E!==t.BYTE&&E.bit<_.bit)throw new Error('"'+g+'" cannot be encoded with mode '+t.toString(E)+`.
 Suggested mode is: `+t.toString(_));switch(E===t.KANJI&&!l.isKanjiModeEnabled()&&(E=t.BYTE),E){case t.NUMERIC:return new r(g);case t.ALPHANUMERIC:return new n(g);case t.KANJI:return new i(g);case t.BYTE:return new s(g)}}e.fromArray=function(k){return k.reduce(function(E,_){return typeof _=="string"?E.push(M(_,null)):_.data&&E.push(M(_.data,_.mode)),E},[])},e.fromString=function(k,E){const _=u(k,l.isKanjiModeEnabled()),U=p(_),T=b(U,E),N=o.find_path(T.map,"start","end"),x=[];for(let S=1;S<N.length-1;S++)x.push(T.table[N[S]].node);return e.fromArray(f(x))},e.rawSplit=function(k){return e.fromArray(u(k,l.isKanjiModeEnabled()))}})(Zt)),Zt}var un;function $s(){if(un)return Lt;un=1;const e=Ae(),t=yr(),r=Ms(),n=Ts(),s=Ds(),i=ks(),a=Cs(),l=kn(),o=Ns(),d=Os(),c=As(),u=Fe(),h=Is();function f(T,N){const x=T.size,S=i.getPositions(N);for(let O=0;O<S.length;O++){const C=S[O][0],A=S[O][1];for(let v=-1;v<=7;v++)if(!(C+v<=-1||x<=C+v))for(let F=-1;F<=7;F++)A+F<=-1||x<=A+F||(v>=0&&v<=6&&(F===0||F===6)||F>=0&&F<=6&&(v===0||v===6)||v>=2&&v<=4&&F>=2&&F<=4?T.set(C+v,A+F,!0,!0):T.set(C+v,A+F,!1,!0))}}function p(T){const N=T.size;for(let x=8;x<N-8;x++){const S=x%2===0;T.set(x,6,S,!0),T.set(6,x,S,!0)}}function b(T,N){const x=s.getPositions(N);for(let S=0;S<x.length;S++){const O=x[S][0],C=x[S][1];for(let A=-2;A<=2;A++)for(let v=-2;v<=2;v++)A===-2||A===2||v===-2||v===2||A===0&&v===0?T.set(O+A,C+v,!0,!0):T.set(O+A,C+v,!1,!0)}}function M(T,N){const x=T.size,S=d.getEncodedBits(N);let O,C,A;for(let v=0;v<18;v++)O=Math.floor(v/3),C=v%3+x-8-3,A=(S>>v&1)===1,T.set(O,C,A,!0),T.set(C,O,A,!0)}function g(T,N,x){const S=T.size,O=c.getEncodedBits(N,x);let C,A;for(C=0;C<15;C++)A=(O>>C&1)===1,C<6?T.set(C,8,A,!0):C<8?T.set(C+1,8,A,!0):T.set(S-15+C,8,A,!0),C<8?T.set(8,S-C-1,A,!0):C<9?T.set(8,15-C-1+1,A,!0):T.set(8,15-C-1,A,!0);T.set(S-8,8,1,!0)}function k(T,N){const x=T.size;let S=-1,O=x-1,C=7,A=0;for(let v=x-1;v>0;v-=2)for(v===6&&v--;;){for(let F=0;F<2;F++)if(!T.isReserved(O,v-F)){let se=!1;A<N.length&&(se=(N[A]>>>C&1)===1),T.set(O,v-F,se),C--,C===-1&&(A++,C=7)}if(O+=S,O<0||x<=O){O-=S,S=-S;break}}}function E(T,N,x){const S=new r;x.forEach(function(F){S.put(F.mode.bit,4),S.put(F.getLength(),u.getCharCountIndicator(F.mode,T)),F.write(S)});const O=e.getSymbolTotalCodewords(T),C=l.getTotalCodewordsCount(T,N),A=(O-C)*8;for(S.getLengthInBits()+4<=A&&S.put(0,4);S.getLengthInBits()%8!==0;)S.putBit(0);const v=(A-S.getLengthInBits())/8;for(let F=0;F<v;F++)S.put(F%2?17:236,8);return _(S,T,N)}function _(T,N,x){const S=e.getSymbolTotalCodewords(N),O=l.getTotalCodewordsCount(N,x),C=S-O,A=l.getBlocksCount(N,x),v=S%A,F=A-v,se=Math.floor(S/A),Ee=Math.floor(C/A),at=Ee+1,je=se-Ee,ot=new o(je);let qe=0;const lt=new Array(A),$r=new Array(A);let At=0;const bs=new Uint8Array(T.buffer);for(let Pe=0;Pe<A;Pe++){const Rt=Pe<F?Ee:at;lt[Pe]=bs.slice(qe,qe+Rt),$r[Pe]=ot.encode(lt[Pe]),qe+=Rt,At=Math.max(At,Rt)}const Ft=new Uint8Array(S);let Ur=0,ue,he;for(ue=0;ue<At;ue++)for(he=0;he<A;he++)ue<lt[he].length&&(Ft[Ur++]=lt[he][ue]);for(ue=0;ue<je;ue++)for(he=0;he<A;he++)Ft[Ur++]=$r[he][ue];return Ft}function U(T,N,x,S){let O;if(Array.isArray(T))O=h.fromArray(T);else if(typeof T=="string"){let se=N;if(!se){const Ee=h.rawSplit(T);se=d.getBestVersionForData(Ee,x)}O=h.fromString(T,se||40)}else throw new Error("Invalid data");const C=d.getBestVersionForData(O,x);if(!C)throw new Error("The amount of data is too big to be stored in a QR Code");if(!N)N=C;else if(N<C)throw new Error(`
The chosen QR Code version cannot contain this amount of data.
Minimum version required to store current data is: `+C+`.
`);const A=E(N,x,O),v=e.getSymbolSize(N),F=new n(v);return f(F,N),p(F),b(F,N),g(F,x,0),N>=7&&M(F,N),k(F,A),isNaN(S)&&(S=a.getBestMask(F,g.bind(null,F,x))),a.applyMask(S,F),g(F,x,S),{modules:F,version:N,errorCorrectionLevel:x,maskPattern:S,segments:O}}return Lt.create=function(N,x){if(typeof N>"u"||N==="")throw new Error("No input text");let S=t.M,O,C;return typeof x<"u"&&(S=t.from(x.errorCorrectionLevel,t.M),O=d.from(x.version),C=a.from(x.maskPattern),x.toSJISFunc&&e.setToSJISFunction(x.toSJISFunc)),U(N,O,S,C)},Lt}var tr={},rr={},hn;function xn(){return hn||(hn=1,(function(e){function t(r){if(typeof r=="number"&&(r=r.toString()),typeof r!="string")throw new Error("Color should be defined as hex string");let n=r.slice().replace("#","").split("");if(n.length<3||n.length===5||n.length>8)throw new Error("Invalid hex color: "+r);(n.length===3||n.length===4)&&(n=Array.prototype.concat.apply([],n.map(function(i){return[i,i]}))),n.length===6&&n.push("F","F");const s=parseInt(n.join(""),16);return{r:s>>24&255,g:s>>16&255,b:s>>8&255,a:s&255,hex:"#"+n.slice(0,6).join("")}}e.getOptions=function(n){n||(n={}),n.color||(n.color={});const s=typeof n.margin>"u"||n.margin===null||n.margin<0?4:n.margin,i=n.width&&n.width>=21?n.width:void 0,a=n.scale||4;return{width:i,scale:i?4:a,margin:s,color:{dark:t(n.color.dark||"#000000ff"),light:t(n.color.light||"#ffffffff")},type:n.type,rendererOpts:n.rendererOpts||{}}},e.getScale=function(n,s){return s.width&&s.width>=n+s.margin*2?s.width/(n+s.margin*2):s.scale},e.getImageWidth=function(n,s){const i=e.getScale(n,s);return Math.floor((n+s.margin*2)*i)},e.qrToImageData=function(n,s,i){const a=s.modules.size,l=s.modules.data,o=e.getScale(a,i),d=Math.floor((a+i.margin*2)*o),c=i.margin*o,u=[i.color.light,i.color.dark];for(let h=0;h<d;h++)for(let f=0;f<d;f++){let p=(h*d+f)*4,b=i.color.light;if(h>=c&&f>=c&&h<d-c&&f<d-c){const M=Math.floor((h-c)/o),g=Math.floor((f-c)/o);b=u[l[M*a+g]?1:0]}n[p++]=b.r,n[p++]=b.g,n[p++]=b.b,n[p]=b.a}}})(rr)),rr}var fn;function Us(){return fn||(fn=1,(function(e){const t=xn();function r(s,i,a){s.clearRect(0,0,i.width,i.height),i.style||(i.style={}),i.height=a,i.width=a,i.style.height=a+"px",i.style.width=a+"px"}function n(){try{return document.createElement("canvas")}catch{throw new Error("You need to specify a canvas element")}}e.render=function(i,a,l){let o=l,d=a;typeof o>"u"&&(!a||!a.getContext)&&(o=a,a=void 0),a||(d=n()),o=t.getOptions(o);const c=t.getImageWidth(i.modules.size,o),u=d.getContext("2d"),h=u.createImageData(c,c);return t.qrToImageData(h.data,i,o),r(u,d,c),u.putImageData(h,0,0),d},e.renderToDataURL=function(i,a,l){let o=l;typeof o>"u"&&(!a||!a.getContext)&&(o=a,a=void 0),o||(o={});const d=e.render(i,a,o),c=o.type||"image/png",u=o.rendererOpts||{};return d.toDataURL(c,u.quality)}})(tr)),tr}var nr={},mn;function Bs(){if(mn)return nr;mn=1;const e=xn();function t(s,i){const a=s.a/255,l=i+'="'+s.hex+'"';return a<1?l+" "+i+'-opacity="'+a.toFixed(2).slice(1)+'"':l}function r(s,i,a){let l=s+i;return typeof a<"u"&&(l+=" "+a),l}function n(s,i,a){let l="",o=0,d=!1,c=0;for(let u=0;u<s.length;u++){const h=Math.floor(u%i),f=Math.floor(u/i);!h&&!d&&(d=!0),s[u]?(c++,u>0&&h>0&&s[u-1]||(l+=d?r("M",h+a,.5+f+a):r("m",o,0),o=0,d=!1),h+1<i&&s[u+1]||(l+=r("h",c),c=0)):o++}return l}return nr.render=function(i,a,l){const o=e.getOptions(a),d=i.modules.size,c=i.modules.data,u=d+o.margin*2,h=o.color.light.a?"<path "+t(o.color.light,"fill")+' d="M0 0h'+u+"v"+u+'H0z"/>':"",f="<path "+t(o.color.dark,"stroke")+' d="'+n(c,d,o.margin)+'"/>',p='viewBox="0 0 '+u+" "+u+'"',M='<svg xmlns="http://www.w3.org/2000/svg" '+(o.width?'width="'+o.width+'" height="'+o.width+'" ':"")+p+' shape-rendering="crispEdges">'+h+f+`</svg>
`;return typeof l=="function"&&l(null,M),M},nr}var gn;function Ws(){if(gn)return Le;gn=1;const e=Ss(),t=$s(),r=Us(),n=Bs();function s(i,a,l,o,d){const c=[].slice.call(arguments,1),u=c.length,h=typeof c[u-1]=="function";if(!h&&!e())throw new Error("Callback required as last argument");if(h){if(u<2)throw new Error("Too few arguments provided");u===2?(d=l,l=a,a=o=void 0):u===3&&(a.getContext&&typeof d>"u"?(d=o,o=void 0):(d=o,o=l,l=a,a=void 0))}else{if(u<1)throw new Error("Too few arguments provided");return u===1?(l=a,a=o=void 0):u===2&&!a.getContext&&(o=l,l=a,a=void 0),new Promise(function(f,p){try{const b=t.create(l,o);f(i(b,a,o))}catch(b){p(b)}})}try{const f=t.create(l,o);d(null,i(f,a,o))}catch(f){d(f)}}return Le.create=t.create,Le.toCanvas=s.bind(null,r.render),Le.toDataURL=s.bind(null,r.renderToDataURL),Le.toString=s.bind(null,function(i,a,l){return n.render(i,l)}),Le}var zs=Ws();const fe=Dn(zs);var Z={},yn;function Hs(){if(yn)return Z;yn=1,Object.defineProperty(Z,"__esModule",{value:!0}),Z.Printd=Z.createIFrame=Z.createLinkStyle=Z.createStyle=void 0;var e=/^(((http[s]?)|file):)?(\/\/)+([0-9a-zA-Z-_.=?&].+)$/,t=/^((\.|\.\.)?\/)([0-9a-zA-Z-_.=?&]+\/)*([0-9a-zA-Z-_.=?&]+)$/,r=function(o){return e.test(o)||t.test(o)};function n(o,d){var c=o.createElement("style");return c.appendChild(o.createTextNode(d)),c}Z.createStyle=n;function s(o,d){var c=o.createElement("link");return c.type="text/css",c.rel="stylesheet",c.href=d,c}Z.createLinkStyle=s;function i(o){var d=window.document.createElement("iframe");return d.setAttribute("src","about:blank"),d.setAttribute("style","visibility:hidden;width:0;height:0;position:absolute;z-index:-9999;bottom:0;"),d.setAttribute("width","0"),d.setAttribute("height","0"),d.setAttribute("wmode","opaque"),o.appendChild(d),d}Z.createIFrame=i;var a={parent:window.document.body,headElements:[],bodyElements:[]},l=(function(){function o(d){this.isLoading=!1,this.hasEvents=!1,this.opts=[a,d||{}].reduce(function(c,u){return Object.keys(u).forEach(function(h){return c[h]=u[h]}),c},{}),this.iframe=i(this.opts.parent)}return o.prototype.getIFrame=function(){return this.iframe},o.prototype.print=function(d,c,u,h){if(!this.isLoading){var f=this.iframe,p=f.contentDocument,b=f.contentWindow;if(!(!p||!b)&&(this.iframe.src="about:blank",this.elCopy=d.cloneNode(!0),!!this.elCopy)){this.isLoading=!0,this.callback=h;var M=b.document;M.open(),M.write('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body></body></html>'),this.addEvents();var g=this.opts,k=g.headElements,E=g.bodyElements;Array.isArray(k)&&k.forEach(function(_){return M.head.appendChild(_)}),Array.isArray(E)&&E.forEach(function(_){return M.body.appendChild(_)}),Array.isArray(c)&&c.forEach(function(_){_&&M.head.appendChild(r(_)?s(M,_):n(M,_))}),M.body.appendChild(this.elCopy),Array.isArray(u)&&u.forEach(function(_){if(_){var U=M.createElement("script");r(_)?U.src=_:U.innerText=_,M.body.appendChild(U)}}),M.close()}}},o.prototype.printURL=function(d,c){this.isLoading||(this.addEvents(),this.isLoading=!0,this.callback=c,this.iframe.src=d)},o.prototype.onBeforePrint=function(d){this.onbeforeprint=d},o.prototype.onAfterPrint=function(d){this.onafterprint=d},o.prototype.launchPrint=function(d){this.isLoading||d.print()},o.prototype.addEvents=function(){var d=this;if(!this.hasEvents){this.hasEvents=!0,this.iframe.addEventListener("load",function(){return d.onLoad()},!1);var c=this.iframe.contentWindow;c&&(this.onbeforeprint&&c.addEventListener("beforeprint",this.onbeforeprint),this.onafterprint&&c.addEventListener("afterprint",this.onafterprint))}},o.prototype.onLoad=function(){var d=this;if(this.iframe){this.isLoading=!1;var c=this.iframe,u=c.contentDocument,h=c.contentWindow;if(!u||!h)return;typeof this.callback=="function"?this.callback({iframe:this.iframe,element:this.elCopy,launchPrint:function(){return d.launchPrint(h)}}):this.launchPrint(h)}},o})();return Z.Printd=l,Z.default=l,Z}var X=Hs(),sr,pn;function Vs(){if(pn)return sr;pn=1;class e{constructor(){this.units=["cero","uno","dos","tres","cuatro","cinco","seis","siete","ocho","nueve"],this.tenToSixteen=["diez","once","doce","trece","catorce","quince","dieciséis"],this.tens=["treinta","cuarenta","cincuenta","sesenta","setenta","ochenta","noventa"]}convertirNroMesAtexto(r){switch(typeof r=="number"&&(r=String(r)),r=this.deleteZerosLeft(r),r){case"1":return"Enero";case"2":return"Febrero";case"3":return"Marzo";case"4":return"Abril";case"5":return"Mayo";case"6":return"Junio";case"7":return"Julio";case"8":return"Agosto";case"9":return"Septiembre";case"10":return"Octubre";case"11":return"Noviembre";case"12":return"Diciembre";default:throw"Numero de mes inválido"}}convertToText(r){if(typeof r=="number"&&(r=String(r)),r=this.deleteZerosLeft(r),!this.validateNumber(r))throw"Número inválido, no se puede convertir!";return this.getName(r)}deleteZerosLeft(r){let n=0,s=!0;for(n=0;n<r.length;n++)if(r.charAt(n)!=0){s=!1;break}return s?"0":r.substr(n)}validateNumber(r){return!(isNaN(r)||r===""||r.indexOf(".")>=0||r.indexOf("-")>=0)}getName(r){return r=this.deleteZerosLeft(r),r.length===1?this.getUnits(r):r.length===2?this.getTens(r):r.length===3?this.getHundreds(r):r.length<7?this.getThousands(r):r.length<13?this.getPeriod(r,6,"millón"):r.length<19?this.getPeriod(r,12,"billón"):"Número demasiado grande."}getUnits(r){let n=parseInt(r);return this.units[n]}getTens(r){let n=r.charAt(1);if(r<17)return this.tenToSixteen[r-10];if(r<20)return"dieci"+this.getUnits(n);switch(r){case"20":return"veinte";case"22":return"veintidós";case"23":return"veintitrés";case"26":return"veintiséis"}if(r<30)return"veinti"+this.getUnits(n);let s=this.tens[r.charAt(0)-3];return n>0&&(s+=" y "+this.getUnits(n)),s}getHundreds(r){let n="",s=r.charAt(0),i=r.substr(1);if(r==100)return"cien";switch(s){case"1":n="ciento";break;case"5":n="quinientos";break;case"7":n="setecientos";break;case"9":n="novecientos"}return n===""&&(n=this.getUnits(s)+"cientos"),i>0&&(n+=" "+this.getName(i)),n}getThousands(r){let n="mil",s=r.length-3,i=r.substr(0,s),a=r.substr(s);return i>1&&(n=this.getName(i).replace("uno","un")+" mil"),a>0&&(n+=" "+this.getName(a)),n}getPeriod(r,n,s){let i="un "+s,a=r.length-n,l=r.substr(0,a),o=r.substr(a);return l>1&&(i=this.getName(l).replace("uno","un")+" "+s.replace("ó","o")+"es"),o>0&&(i+=" "+this.getName(o)),i}}return sr={conversorNumerosALetras:e},sr}var js=Vs();const me=Dn(js);var ct={},vn;function qs(){if(vn)return ct;vn=1,Object.defineProperty(ct,"__esModule",{value:!0});function e(o){switch(o){case 1:return"Un";case 2:return"Dos";case 3:return"Tres";case 4:return"Cuatro";case 5:return"Cinco";case 6:return"Seis";case 7:return"Siete";case 8:return"Ocho";case 9:return"Nueve";default:return""}}function t(o,d){return d>0?o+" y "+e(d):o}function r(o){var d=Math.floor(o/10),c=o-d*10;switch(d){case 1:switch(c){case 0:return"Diez";case 1:return"Once";case 2:return"Doce";case 3:return"Trece";case 4:return"Catorce";case 5:return"Quince";default:return"Dieci"+e(c).toLowerCase()}case 2:return c===0?"Veinte":"Veinti"+e(c).toLowerCase();case 3:return t("Treinta",c);case 4:return t("Cuarenta",c);case 5:return t("Cincuenta",c);case 6:return t("Sesenta",c);case 7:return t("Setenta",c);case 8:return t("Ochenta",c);case 9:return t("Noventa",c);case 0:return e(c);default:return""}}function n(o){var d=Math.floor(o/100),c=o-d*100;switch(d){case 1:return c>0?"Ciento "+r(c):"Cien";case 2:return"Doscientos "+r(c);case 3:return"Trescientos "+r(c);case 4:return"Cuatrocientos "+r(c);case 5:return"Quinientos "+r(c);case 6:return"Seiscientos "+r(c);case 7:return"Setecientos "+r(c);case 8:return"Ochocientos "+r(c);case 9:return"Novecientos "+r(c);default:return r(c)}}function s(o,d,c,u){var h=Math.floor(o/d),f=o-h*d,p="";return h>0&&(h>1?p=n(h)+" "+u:p=c),f>0&&(p+=""),p}function i(o){var d=1e3,c=Math.floor(o/d),u=o-c*d,h=s(o,d,"Un Mil","Mil"),f=n(u);return h===""?f:(h+" "+f).trim()}function a(o){var d=1e6,c=Math.floor(o/d),u=o-c*d,h=s(o,d,"Un Millón de","Millones de"),f=i(u);return h===""?f:(h+" "+f).trim()}function l(o){var d={enteros:Math.floor(o),centavos:Math.round(o*100)-Math.floor(o)*100,letrasCentavos:"",letrasMonedaPlural:"Pesos",letrasMonedaSingular:"Peso",letrasMonedaCentavoPlural:"/100 M.N.",letrasMonedaCentavoSingular:"/100 M.N."};return d.centavos>=0&&(d.letrasCentavos=(function(){return d.centavos>=1&d.centavos<=9?"0"+d.centavos+d.letrasMonedaCentavoSingular:d.centavos===0?"00"+d.letrasMonedaCentavoSingular:d.centavos+d.letrasMonedaCentavoPlural})()),d.enteros===0?("Cero "+d.letrasMonedaPlural+" "+d.letrasCentavos).trim():d.enteros===1?(a(d.enteros)+" "+d.letrasMonedaSingular+" "+d.letrasCentavos).trim():(a(d.enteros)+" "+d.letrasMonedaPlural+" "+d.letrasCentavos).trim()}return ct.NumerosALetras=l,ct}qs();var Nn;function y(){return Nn.apply(null,arguments)}function Gs(e){Nn=e}function te(e){return e instanceof Array||Object.prototype.toString.call(e)==="[object Array]"}function Oe(e){return e!=null&&Object.prototype.toString.call(e)==="[object Object]"}function Y(e,t){return Object.prototype.hasOwnProperty.call(e,t)}function pr(e){if(Object.getOwnPropertyNames)return Object.getOwnPropertyNames(e).length===0;var t;for(t in e)if(Y(e,t))return!1;return!0}function q(e){return e===void 0}function be(e){return typeof e=="number"||Object.prototype.toString.call(e)==="[object Number]"}function rt(e){return e instanceof Date||Object.prototype.toString.call(e)==="[object Date]"}function On(e,t){var r=[],n,s=e.length;for(n=0;n<s;++n)r.push(t(e[n],n));return r}function De(e,t){for(var r in t)Y(t,r)&&(e[r]=t[r]);return Y(t,"toString")&&(e.toString=t.toString),Y(t,"valueOf")&&(e.valueOf=t.valueOf),e}function de(e,t,r,n){return es(e,t,r,n,!0).utc()}function Zs(){return{empty:!1,unusedTokens:[],unusedInput:[],overflow:-2,charsLeftOver:0,nullInput:!1,invalidEra:null,invalidMonth:null,invalidFormat:!1,userInvalidated:!1,iso:!1,parsedDateParts:[],era:null,meridiem:null,rfc2822:!1,weekdayMismatch:!1}}function R(e){return e._pf==null&&(e._pf=Zs()),e._pf}var dr;Array.prototype.some?dr=Array.prototype.some:dr=function(e){var t=Object(this),r=t.length>>>0,n;for(n=0;n<r;n++)if(n in t&&e.call(this,t[n],n,t))return!0;return!1};function vr(e){var t=null,r=!1,n=e._d&&!isNaN(e._d.getTime());if(n&&(t=R(e),r=dr.call(t.parsedDateParts,function(s){return s!=null}),n=t.overflow<0&&!t.empty&&!t.invalidEra&&!t.invalidMonth&&!t.invalidWeekday&&!t.weekdayMismatch&&!t.nullInput&&!t.invalidFormat&&!t.userInvalidated&&(!t.meridiem||t.meridiem&&r),e._strict&&(n=n&&t.charsLeftOver===0&&t.unusedTokens.length===0&&t.bigHour===void 0)),Object.isFrozen==null||!Object.isFrozen(e))e._isValid=n;else return n;return e._isValid}function bt(e){var t=de(NaN);return e!=null?De(R(t),e):R(t).userInvalidated=!0,t}var wn=y.momentProperties=[],ir=!1;function wr(e,t){var r,n,s,i=wn.length;if(q(t._isAMomentObject)||(e._isAMomentObject=t._isAMomentObject),q(t._i)||(e._i=t._i),q(t._f)||(e._f=t._f),q(t._l)||(e._l=t._l),q(t._strict)||(e._strict=t._strict),q(t._tzm)||(e._tzm=t._tzm),q(t._isUTC)||(e._isUTC=t._isUTC),q(t._offset)||(e._offset=t._offset),q(t._pf)||(e._pf=R(t)),q(t._locale)||(e._locale=t._locale),i>0)for(r=0;r<i;r++)n=wn[r],s=t[n],q(s)||(e[n]=s);return e}function nt(e){wr(this,e),this._d=new Date(e._d!=null?e._d.getTime():NaN),this.isValid()||(this._d=new Date(NaN)),ir===!1&&(ir=!0,y.updateOffset(this),ir=!1)}function re(e){return e instanceof nt||e!=null&&e._isAMomentObject!=null}function An(e){y.suppressDeprecationWarnings===!1&&typeof console<"u"&&console.warn&&console.warn("Deprecation warning: "+e)}function K(e,t){var r=!0;return De(function(){if(y.deprecationHandler!=null&&y.deprecationHandler(null,e),r){var n=[],s,i,a,l=arguments.length;for(i=0;i<l;i++){if(s="",typeof arguments[i]=="object"){s+=`
[`+i+"] ";for(a in arguments[0])Y(arguments[0],a)&&(s+=a+": "+arguments[0][a]+", ");s=s.slice(0,-2)}else s=arguments[i];n.push(s)}An(e+`
Arguments: `+Array.prototype.slice.call(n).join("")+`
`+new Error().stack),r=!1}return t.apply(this,arguments)},t)}var _n={};function Fn(e,t){y.deprecationHandler!=null&&y.deprecationHandler(e,t),_n[e]||(An(t),_n[e]=!0)}y.suppressDeprecationWarnings=!1;y.deprecationHandler=null;function ce(e){return typeof Function<"u"&&e instanceof Function||Object.prototype.toString.call(e)==="[object Function]"}function Js(e){var t,r;for(r in e)Y(e,r)&&(t=e[r],ce(t)?this[r]=t:this["_"+r]=t);this._config=e,this._dayOfMonthOrdinalParseLenient=new RegExp((this._dayOfMonthOrdinalParse.source||this._ordinalParse.source)+"|"+/\d{1,2}/.source)}function cr(e,t){var r=De({},e),n;for(n in t)Y(t,n)&&(Oe(e[n])&&Oe(t[n])?(r[n]={},De(r[n],e[n]),De(r[n],t[n])):t[n]!=null?r[n]=t[n]:delete r[n]);for(n in e)Y(e,n)&&!Y(t,n)&&Oe(e[n])&&(r[n]=De({},r[n]));return r}function _r(e){e!=null&&this.set(e)}var ur;Object.keys?ur=Object.keys:ur=function(e){var t,r=[];for(t in e)Y(e,t)&&r.push(t);return r};var Ks={sameDay:"[Today at] LT",nextDay:"[Tomorrow at] LT",nextWeek:"dddd [at] LT",lastDay:"[Yesterday at] LT",lastWeek:"[Last] dddd [at] LT",sameElse:"L"};function Qs(e,t,r){var n=this._calendar[e]||this._calendar.sameElse;return ce(n)?n.call(t,r):n}function le(e,t,r){var n=""+Math.abs(e),s=t-n.length,i=e>=0;return(i?r?"+":"":"-")+Math.pow(10,Math.max(0,s)).toString().substr(1)+n}var br=/(\[[^\[]*\])|(\\)?([Hh]mm(ss)?|Mo|MM?M?M?|Do|DDDo|DD?D?D?|ddd?d?|do?|w[o|w]?|W[o|W]?|Qo?|N{1,5}|YYYYYY|YYYYY|YYYY|YY|y{2,4}|yo?|gg(ggg?)?|GG(GGG?)?|e|E|a|A|hh?|HH?|kk?|mm?|ss?|S{1,9}|x|X|zz?|ZZ?|.)/g,ut=/(\[[^\[]*\])|(\\)?(LTS|LT|LL?L?L?|l{1,4})/g,ar={},Ue={};function D(e,t,r,n){var s=n;typeof n=="string"&&(s=function(){return this[n]()}),e&&(Ue[e]=s),t&&(Ue[t[0]]=function(){return le(s.apply(this,arguments),t[1],t[2])}),r&&(Ue[r]=function(){return this.localeData().ordinal(s.apply(this,arguments),e)})}function Xs(e){return e.match(/\[[\s\S]/)?e.replace(/^\[|\]$/g,""):e.replace(/\\/g,"")}function ei(e){var t=e.match(br),r,n;for(r=0,n=t.length;r<n;r++)Ue[t[r]]?t[r]=Ue[t[r]]:t[r]=Xs(t[r]);return function(s){var i="",a;for(a=0;a<n;a++)i+=ce(t[a])?t[a].call(s,e):t[a];return i}}function ft(e,t){return e.isValid()?(t=Rn(t,e.localeData()),ar[t]=ar[t]||ei(t),ar[t](e)):e.localeData().invalidDate()}function Rn(e,t){var r=5;function n(s){return t.longDateFormat(s)||s}for(ut.lastIndex=0;r>=0&&ut.test(e);)e=e.replace(ut,n),ut.lastIndex=0,r-=1;return e}var ti={LTS:"h:mm:ss A",LT:"h:mm A",L:"MM/DD/YYYY",LL:"MMMM D, YYYY",LLL:"MMMM D, YYYY h:mm A",LLLL:"dddd, MMMM D, YYYY h:mm A"};function ri(e){var t=this._longDateFormat[e],r=this._longDateFormat[e.toUpperCase()];return t||!r?t:(this._longDateFormat[e]=r.match(br).map(function(n){return n==="MMMM"||n==="MM"||n==="DD"||n==="dddd"?n.slice(1):n}).join(""),this._longDateFormat[e])}var ni="Invalid date";function si(){return this._invalidDate}var ii="%d",ai=/\d{1,2}/;function oi(e){return this._ordinal.replace("%d",e)}var li={future:"in %s",past:"%s ago",s:"a few seconds",ss:"%d seconds",m:"a minute",mm:"%d minutes",h:"an hour",hh:"%d hours",d:"a day",dd:"%d days",w:"a week",ww:"%d weeks",M:"a month",MM:"%d months",y:"a year",yy:"%d years"};function di(e,t,r,n){var s=this._relativeTime[r];return ce(s)?s(e,t,r,n):s.replace(/%d/i,e)}function ci(e,t){var r=this._relativeTime[e>0?"future":"past"];return ce(r)?r(t):r.replace(/%s/i,t)}var bn={D:"date",dates:"date",date:"date",d:"day",days:"day",day:"day",e:"weekday",weekdays:"weekday",weekday:"weekday",E:"isoWeekday",isoweekdays:"isoWeekday",isoweekday:"isoWeekday",DDD:"dayOfYear",dayofyears:"dayOfYear",dayofyear:"dayOfYear",h:"hour",hours:"hour",hour:"hour",ms:"millisecond",milliseconds:"millisecond",millisecond:"millisecond",m:"minute",minutes:"minute",minute:"minute",M:"month",months:"month",month:"month",Q:"quarter",quarters:"quarter",quarter:"quarter",s:"second",seconds:"second",second:"second",gg:"weekYear",weekyears:"weekYear",weekyear:"weekYear",GG:"isoWeekYear",isoweekyears:"isoWeekYear",isoweekyear:"isoWeekYear",w:"week",weeks:"week",week:"week",W:"isoWeek",isoweeks:"isoWeek",isoweek:"isoWeek",y:"year",years:"year",year:"year"};function Q(e){return typeof e=="string"?bn[e]||bn[e.toLowerCase()]:void 0}function Sr(e){var t={},r,n;for(n in e)Y(e,n)&&(r=Q(n),r&&(t[r]=e[n]));return t}var ui={date:9,day:11,weekday:11,isoWeekday:11,dayOfYear:4,hour:13,millisecond:16,minute:14,month:8,quarter:7,second:15,weekYear:1,isoWeekYear:1,week:5,isoWeek:5,year:1};function hi(e){var t=[],r;for(r in e)Y(e,r)&&t.push({unit:r,priority:ui[r]});return t.sort(function(n,s){return n.priority-s.priority}),t}var Pn=/\d/,G=/\d\d/,Ln=/\d{3}/,Mr=/\d{4}/,St=/[+-]?\d{6}/,W=/\d\d?/,Yn=/\d\d\d\d?/,In=/\d\d\d\d\d\d?/,Mt=/\d{1,3}/,Tr=/\d{1,4}/,Tt=/[+-]?\d{1,6}/,ze=/\d+/,Dt=/[+-]?\d+/,fi=/Z|[+-]\d\d:?\d\d/gi,kt=/Z|[+-]\d\d(?::?\d\d)?/gi,mi=/[+-]?\d+(\.\d{1,3})?/,st=/[0-9]{0,256}['a-z\u00A0-\u05FF\u0700-\uD7FF\uF900-\uFDCF\uFDF0-\uFF07\uFF10-\uFFEF]{1,256}|[\u0600-\u06FF\/]{1,256}(\s*?[\u0600-\u06FF]{1,256}){1,2}/i,He=/^[1-9]\d?/,Dr=/^([1-9]\d|\d)/,yt;yt={};function w(e,t,r){yt[e]=ce(t)?t:function(n,s){return n&&r?r:t}}function gi(e,t){return Y(yt,e)?yt[e](t._strict,t._locale):new RegExp(yi(e))}function yi(e){return we(e.replace("\\","").replace(/\\(\[)|\\(\])|\[([^\]\[]*)\]|\\(.)/g,function(t,r,n,s,i){return r||n||s||i}))}function we(e){return e.replace(/[-\/\\^$*+?.()|[\]{}]/g,"\\$&")}function J(e){return e<0?Math.ceil(e)||0:Math.floor(e)}function P(e){var t=+e,r=0;return t!==0&&isFinite(t)&&(r=J(t)),r}var hr={};function $(e,t){var r,n=t,s;for(typeof e=="string"&&(e=[e]),be(t)&&(n=function(i,a){a[t]=P(i)}),s=e.length,r=0;r<s;r++)hr[e[r]]=n}function it(e,t){$(e,function(r,n,s,i){s._w=s._w||{},t(r,s._w,s,i)})}function pi(e,t,r){t!=null&&Y(hr,e)&&hr[e](t,r._a,r,e)}function Ct(e){return e%4===0&&e%100!==0||e%400===0}var j=0,pe=1,oe=2,V=3,ee=4,ve=5,Ne=6,vi=7,wi=8;D("Y",0,0,function(){var e=this.year();return e<=9999?le(e,4):"+"+e});D(0,["YY",2],0,function(){return this.year()%100});D(0,["YYYY",4],0,"year");D(0,["YYYYY",5],0,"year");D(0,["YYYYYY",6,!0],0,"year");w("Y",Dt);w("YY",W,G);w("YYYY",Tr,Mr);w("YYYYY",Tt,St);w("YYYYYY",Tt,St);$(["YYYYY","YYYYYY"],j);$("YYYY",function(e,t){t[j]=e.length===2?y.parseTwoDigitYear(e):P(e)});$("YY",function(e,t){t[j]=y.parseTwoDigitYear(e)});$("Y",function(e,t){t[j]=parseInt(e,10)});function Ke(e){return Ct(e)?366:365}y.parseTwoDigitYear=function(e){return P(e)+(P(e)>68?1900:2e3)};var $n=Ve("FullYear",!0);function _i(){return Ct(this.year())}function Ve(e,t){return function(r){return r!=null?(Un(this,e,r),y.updateOffset(this,t),this):Qe(this,e)}}function Qe(e,t){if(!e.isValid())return NaN;var r=e._d,n=e._isUTC;switch(t){case"Milliseconds":return n?r.getUTCMilliseconds():r.getMilliseconds();case"Seconds":return n?r.getUTCSeconds():r.getSeconds();case"Minutes":return n?r.getUTCMinutes():r.getMinutes();case"Hours":return n?r.getUTCHours():r.getHours();case"Date":return n?r.getUTCDate():r.getDate();case"Day":return n?r.getUTCDay():r.getDay();case"Month":return n?r.getUTCMonth():r.getMonth();case"FullYear":return n?r.getUTCFullYear():r.getFullYear();default:return NaN}}function Un(e,t,r){var n,s,i,a,l;if(!(!e.isValid()||isNaN(r))){switch(n=e._d,s=e._isUTC,t){case"Milliseconds":return void(s?n.setUTCMilliseconds(r):n.setMilliseconds(r));case"Seconds":return void(s?n.setUTCSeconds(r):n.setSeconds(r));case"Minutes":return void(s?n.setUTCMinutes(r):n.setMinutes(r));case"Hours":return void(s?n.setUTCHours(r):n.setHours(r));case"Date":return void(s?n.setUTCDate(r):n.setDate(r));case"FullYear":break;default:return}i=r,a=e.month(),l=e.date(),l=l===29&&a===1&&!Ct(i)?28:l,s?n.setUTCFullYear(i,a,l):n.setFullYear(i,a,l)}}function bi(e){return e=Q(e),ce(this[e])?this[e]():this}function Si(e,t){if(typeof e=="object"){e=Sr(e);var r=hi(e),n,s=r.length;for(n=0;n<s;n++)this[r[n].unit](e[r[n].unit])}else if(e=Q(e),ce(this[e]))return this[e](t);return this}function Mi(e,t){return(e%t+t)%t}var H;Array.prototype.indexOf?H=Array.prototype.indexOf:H=function(e){var t;for(t=0;t<this.length;++t)if(this[t]===e)return t;return-1};function kr(e,t){if(isNaN(e)||isNaN(t))return NaN;var r=Mi(t,12);return e+=(t-r)/12,r===1?Ct(e)?29:28:31-r%7%2}D("M",["MM",2],"Mo",function(){return this.month()+1});D("MMM",0,0,function(e){return this.localeData().monthsShort(this,e)});D("MMMM",0,0,function(e){return this.localeData().months(this,e)});w("M",W,He);w("MM",W,G);w("MMM",function(e,t){return t.monthsShortRegex(e)});w("MMMM",function(e,t){return t.monthsRegex(e)});$(["M","MM"],function(e,t){t[pe]=P(e)-1});$(["MMM","MMMM"],function(e,t,r,n){var s=r._locale.monthsParse(e,n,r._strict);s!=null?t[pe]=s:R(r).invalidMonth=e});var Ti="January_February_March_April_May_June_July_August_September_October_November_December".split("_"),Bn="Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec".split("_"),Wn=/D[oD]?(\[[^\[\]]*\]|\s)+MMMM?/,Di=st,ki=st;function Ci(e,t){return e?te(this._months)?this._months[e.month()]:this._months[(this._months.isFormat||Wn).test(t)?"format":"standalone"][e.month()]:te(this._months)?this._months:this._months.standalone}function Ei(e,t){return e?te(this._monthsShort)?this._monthsShort[e.month()]:this._monthsShort[Wn.test(t)?"format":"standalone"][e.month()]:te(this._monthsShort)?this._monthsShort:this._monthsShort.standalone}function xi(e,t,r){var n,s,i,a=e.toLocaleLowerCase();if(!this._monthsParse)for(this._monthsParse=[],this._longMonthsParse=[],this._shortMonthsParse=[],n=0;n<12;++n)i=de([2e3,n]),this._shortMonthsParse[n]=this.monthsShort(i,"").toLocaleLowerCase(),this._longMonthsParse[n]=this.months(i,"").toLocaleLowerCase();return r?t==="MMM"?(s=H.call(this._shortMonthsParse,a),s!==-1?s:null):(s=H.call(this._longMonthsParse,a),s!==-1?s:null):t==="MMM"?(s=H.call(this._shortMonthsParse,a),s!==-1?s:(s=H.call(this._longMonthsParse,a),s!==-1?s:null)):(s=H.call(this._longMonthsParse,a),s!==-1?s:(s=H.call(this._shortMonthsParse,a),s!==-1?s:null))}function Ni(e,t,r){var n,s,i;if(this._monthsParseExact)return xi.call(this,e,t,r);for(this._monthsParse||(this._monthsParse=[],this._longMonthsParse=[],this._shortMonthsParse=[]),n=0;n<12;n++){if(s=de([2e3,n]),r&&!this._longMonthsParse[n]&&(this._longMonthsParse[n]=new RegExp("^"+this.months(s,"").replace(".","")+"$","i"),this._shortMonthsParse[n]=new RegExp("^"+this.monthsShort(s,"").replace(".","")+"$","i")),!r&&!this._monthsParse[n]&&(i="^"+this.months(s,"")+"|^"+this.monthsShort(s,""),this._monthsParse[n]=new RegExp(i.replace(".",""),"i")),r&&t==="MMMM"&&this._longMonthsParse[n].test(e))return n;if(r&&t==="MMM"&&this._shortMonthsParse[n].test(e))return n;if(!r&&this._monthsParse[n].test(e))return n}}function zn(e,t){if(!e.isValid())return e;if(typeof t=="string"){if(/^\d+$/.test(t))t=P(t);else if(t=e.localeData().monthsParse(t),!be(t))return e}var r=t,n=e.date();return n=n<29?n:Math.min(n,kr(e.year(),r)),e._isUTC?e._d.setUTCMonth(r,n):e._d.setMonth(r,n),e}function Hn(e){return e!=null?(zn(this,e),y.updateOffset(this,!0),this):Qe(this,"Month")}function Oi(){return kr(this.year(),this.month())}function Ai(e){return this._monthsParseExact?(Y(this,"_monthsRegex")||Vn.call(this),e?this._monthsShortStrictRegex:this._monthsShortRegex):(Y(this,"_monthsShortRegex")||(this._monthsShortRegex=Di),this._monthsShortStrictRegex&&e?this._monthsShortStrictRegex:this._monthsShortRegex)}function Fi(e){return this._monthsParseExact?(Y(this,"_monthsRegex")||Vn.call(this),e?this._monthsStrictRegex:this._monthsRegex):(Y(this,"_monthsRegex")||(this._monthsRegex=ki),this._monthsStrictRegex&&e?this._monthsStrictRegex:this._monthsRegex)}function Vn(){function e(o,d){return d.length-o.length}var t=[],r=[],n=[],s,i,a,l;for(s=0;s<12;s++)i=de([2e3,s]),a=we(this.monthsShort(i,"")),l=we(this.months(i,"")),t.push(a),r.push(l),n.push(l),n.push(a);t.sort(e),r.sort(e),n.sort(e),this._monthsRegex=new RegExp("^("+n.join("|")+")","i"),this._monthsShortRegex=this._monthsRegex,this._monthsStrictRegex=new RegExp("^("+r.join("|")+")","i"),this._monthsShortStrictRegex=new RegExp("^("+t.join("|")+")","i")}function Ri(e,t,r,n,s,i,a){var l;return e<100&&e>=0?(l=new Date(e+400,t,r,n,s,i,a),isFinite(l.getFullYear())&&l.setFullYear(e)):l=new Date(e,t,r,n,s,i,a),l}function Xe(e){var t,r;return e<100&&e>=0?(r=Array.prototype.slice.call(arguments),r[0]=e+400,t=new Date(Date.UTC.apply(null,r)),isFinite(t.getUTCFullYear())&&t.setUTCFullYear(e)):t=new Date(Date.UTC.apply(null,arguments)),t}function pt(e,t,r){var n=7+t-r,s=(7+Xe(e,0,n).getUTCDay()-t)%7;return-s+n-1}function jn(e,t,r,n,s){var i=(7+r-n)%7,a=pt(e,n,s),l=1+7*(t-1)+i+a,o,d;return l<=0?(o=e-1,d=Ke(o)+l):l>Ke(e)?(o=e+1,d=l-Ke(e)):(o=e,d=l),{year:o,dayOfYear:d}}function et(e,t,r){var n=pt(e.year(),t,r),s=Math.floor((e.dayOfYear()-n-1)/7)+1,i,a;return s<1?(a=e.year()-1,i=s+_e(a,t,r)):s>_e(e.year(),t,r)?(i=s-_e(e.year(),t,r),a=e.year()+1):(a=e.year(),i=s),{week:i,year:a}}function _e(e,t,r){var n=pt(e,t,r),s=pt(e+1,t,r);return(Ke(e)-n+s)/7}D("w",["ww",2],"wo","week");D("W",["WW",2],"Wo","isoWeek");w("w",W,He);w("ww",W,G);w("W",W,He);w("WW",W,G);it(["w","ww","W","WW"],function(e,t,r,n){t[n.substr(0,1)]=P(e)});function Pi(e){return et(e,this._week.dow,this._week.doy).week}var Li={dow:0,doy:6};function Yi(){return this._week.dow}function Ii(){return this._week.doy}function $i(e){var t=this.localeData().week(this);return e==null?t:this.add((e-t)*7,"d")}function Ui(e){var t=et(this,1,4).week;return e==null?t:this.add((e-t)*7,"d")}D("d",0,"do","day");D("dd",0,0,function(e){return this.localeData().weekdaysMin(this,e)});D("ddd",0,0,function(e){return this.localeData().weekdaysShort(this,e)});D("dddd",0,0,function(e){return this.localeData().weekdays(this,e)});D("e",0,0,"weekday");D("E",0,0,"isoWeekday");w("d",W);w("e",W);w("E",W);w("dd",function(e,t){return t.weekdaysMinRegex(e)});w("ddd",function(e,t){return t.weekdaysShortRegex(e)});w("dddd",function(e,t){return t.weekdaysRegex(e)});it(["dd","ddd","dddd"],function(e,t,r,n){var s=r._locale.weekdaysParse(e,n,r._strict);s!=null?t.d=s:R(r).invalidWeekday=e});it(["d","e","E"],function(e,t,r,n){t[n]=P(e)});function Bi(e,t){return typeof e!="string"?e:isNaN(e)?(e=t.weekdaysParse(e),typeof e=="number"?e:null):parseInt(e,10)}function Wi(e,t){return typeof e=="string"?t.weekdaysParse(e)%7||7:isNaN(e)?null:e}function Cr(e,t){return e.slice(t,7).concat(e.slice(0,t))}var zi="Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),qn="Sun_Mon_Tue_Wed_Thu_Fri_Sat".split("_"),Hi="Su_Mo_Tu_We_Th_Fr_Sa".split("_"),Vi=st,ji=st,qi=st;function Gi(e,t){var r=te(this._weekdays)?this._weekdays:this._weekdays[e&&e!==!0&&this._weekdays.isFormat.test(t)?"format":"standalone"];return e===!0?Cr(r,this._week.dow):e?r[e.day()]:r}function Zi(e){return e===!0?Cr(this._weekdaysShort,this._week.dow):e?this._weekdaysShort[e.day()]:this._weekdaysShort}function Ji(e){return e===!0?Cr(this._weekdaysMin,this._week.dow):e?this._weekdaysMin[e.day()]:this._weekdaysMin}function Ki(e,t,r){var n,s,i,a=e.toLocaleLowerCase();if(!this._weekdaysParse)for(this._weekdaysParse=[],this._shortWeekdaysParse=[],this._minWeekdaysParse=[],n=0;n<7;++n)i=de([2e3,1]).day(n),this._minWeekdaysParse[n]=this.weekdaysMin(i,"").toLocaleLowerCase(),this._shortWeekdaysParse[n]=this.weekdaysShort(i,"").toLocaleLowerCase(),this._weekdaysParse[n]=this.weekdays(i,"").toLocaleLowerCase();return r?t==="dddd"?(s=H.call(this._weekdaysParse,a),s!==-1?s:null):t==="ddd"?(s=H.call(this._shortWeekdaysParse,a),s!==-1?s:null):(s=H.call(this._minWeekdaysParse,a),s!==-1?s:null):t==="dddd"?(s=H.call(this._weekdaysParse,a),s!==-1||(s=H.call(this._shortWeekdaysParse,a),s!==-1)?s:(s=H.call(this._minWeekdaysParse,a),s!==-1?s:null)):t==="ddd"?(s=H.call(this._shortWeekdaysParse,a),s!==-1||(s=H.call(this._weekdaysParse,a),s!==-1)?s:(s=H.call(this._minWeekdaysParse,a),s!==-1?s:null)):(s=H.call(this._minWeekdaysParse,a),s!==-1||(s=H.call(this._weekdaysParse,a),s!==-1)?s:(s=H.call(this._shortWeekdaysParse,a),s!==-1?s:null))}function Qi(e,t,r){var n,s,i;if(this._weekdaysParseExact)return Ki.call(this,e,t,r);for(this._weekdaysParse||(this._weekdaysParse=[],this._minWeekdaysParse=[],this._shortWeekdaysParse=[],this._fullWeekdaysParse=[]),n=0;n<7;n++){if(s=de([2e3,1]).day(n),r&&!this._fullWeekdaysParse[n]&&(this._fullWeekdaysParse[n]=new RegExp("^"+this.weekdays(s,"").replace(".","\\.?")+"$","i"),this._shortWeekdaysParse[n]=new RegExp("^"+this.weekdaysShort(s,"").replace(".","\\.?")+"$","i"),this._minWeekdaysParse[n]=new RegExp("^"+this.weekdaysMin(s,"").replace(".","\\.?")+"$","i")),this._weekdaysParse[n]||(i="^"+this.weekdays(s,"")+"|^"+this.weekdaysShort(s,"")+"|^"+this.weekdaysMin(s,""),this._weekdaysParse[n]=new RegExp(i.replace(".",""),"i")),r&&t==="dddd"&&this._fullWeekdaysParse[n].test(e))return n;if(r&&t==="ddd"&&this._shortWeekdaysParse[n].test(e))return n;if(r&&t==="dd"&&this._minWeekdaysParse[n].test(e))return n;if(!r&&this._weekdaysParse[n].test(e))return n}}function Xi(e){if(!this.isValid())return e!=null?this:NaN;var t=Qe(this,"Day");return e!=null?(e=Bi(e,this.localeData()),this.add(e-t,"d")):t}function ea(e){if(!this.isValid())return e!=null?this:NaN;var t=(this.day()+7-this.localeData()._week.dow)%7;return e==null?t:this.add(e-t,"d")}function ta(e){if(!this.isValid())return e!=null?this:NaN;if(e!=null){var t=Wi(e,this.localeData());return this.day(this.day()%7?t:t-7)}else return this.day()||7}function ra(e){return this._weekdaysParseExact?(Y(this,"_weekdaysRegex")||Er.call(this),e?this._weekdaysStrictRegex:this._weekdaysRegex):(Y(this,"_weekdaysRegex")||(this._weekdaysRegex=Vi),this._weekdaysStrictRegex&&e?this._weekdaysStrictRegex:this._weekdaysRegex)}function na(e){return this._weekdaysParseExact?(Y(this,"_weekdaysRegex")||Er.call(this),e?this._weekdaysShortStrictRegex:this._weekdaysShortRegex):(Y(this,"_weekdaysShortRegex")||(this._weekdaysShortRegex=ji),this._weekdaysShortStrictRegex&&e?this._weekdaysShortStrictRegex:this._weekdaysShortRegex)}function sa(e){return this._weekdaysParseExact?(Y(this,"_weekdaysRegex")||Er.call(this),e?this._weekdaysMinStrictRegex:this._weekdaysMinRegex):(Y(this,"_weekdaysMinRegex")||(this._weekdaysMinRegex=qi),this._weekdaysMinStrictRegex&&e?this._weekdaysMinStrictRegex:this._weekdaysMinRegex)}function Er(){function e(c,u){return u.length-c.length}var t=[],r=[],n=[],s=[],i,a,l,o,d;for(i=0;i<7;i++)a=de([2e3,1]).day(i),l=we(this.weekdaysMin(a,"")),o=we(this.weekdaysShort(a,"")),d=we(this.weekdays(a,"")),t.push(l),r.push(o),n.push(d),s.push(l),s.push(o),s.push(d);t.sort(e),r.sort(e),n.sort(e),s.sort(e),this._weekdaysRegex=new RegExp("^("+s.join("|")+")","i"),this._weekdaysShortRegex=this._weekdaysRegex,this._weekdaysMinRegex=this._weekdaysRegex,this._weekdaysStrictRegex=new RegExp("^("+n.join("|")+")","i"),this._weekdaysShortStrictRegex=new RegExp("^("+r.join("|")+")","i"),this._weekdaysMinStrictRegex=new RegExp("^("+t.join("|")+")","i")}function xr(){return this.hours()%12||12}function ia(){return this.hours()||24}D("H",["HH",2],0,"hour");D("h",["hh",2],0,xr);D("k",["kk",2],0,ia);D("hmm",0,0,function(){return""+xr.apply(this)+le(this.minutes(),2)});D("hmmss",0,0,function(){return""+xr.apply(this)+le(this.minutes(),2)+le(this.seconds(),2)});D("Hmm",0,0,function(){return""+this.hours()+le(this.minutes(),2)});D("Hmmss",0,0,function(){return""+this.hours()+le(this.minutes(),2)+le(this.seconds(),2)});function Gn(e,t){D(e,0,0,function(){return this.localeData().meridiem(this.hours(),this.minutes(),t)})}Gn("a",!0);Gn("A",!1);function Zn(e,t){return t._meridiemParse}w("a",Zn);w("A",Zn);w("H",W,Dr);w("h",W,He);w("k",W,He);w("HH",W,G);w("hh",W,G);w("kk",W,G);w("hmm",Yn);w("hmmss",In);w("Hmm",Yn);w("Hmmss",In);$(["H","HH"],V);$(["k","kk"],function(e,t,r){var n=P(e);t[V]=n===24?0:n});$(["a","A"],function(e,t,r){r._isPm=r._locale.isPM(e),r._meridiem=e});$(["h","hh"],function(e,t,r){t[V]=P(e),R(r).bigHour=!0});$("hmm",function(e,t,r){var n=e.length-2;t[V]=P(e.substr(0,n)),t[ee]=P(e.substr(n)),R(r).bigHour=!0});$("hmmss",function(e,t,r){var n=e.length-4,s=e.length-2;t[V]=P(e.substr(0,n)),t[ee]=P(e.substr(n,2)),t[ve]=P(e.substr(s)),R(r).bigHour=!0});$("Hmm",function(e,t,r){var n=e.length-2;t[V]=P(e.substr(0,n)),t[ee]=P(e.substr(n))});$("Hmmss",function(e,t,r){var n=e.length-4,s=e.length-2;t[V]=P(e.substr(0,n)),t[ee]=P(e.substr(n,2)),t[ve]=P(e.substr(s))});function aa(e){return(e+"").toLowerCase().charAt(0)==="p"}var oa=/[ap]\.?m?\.?/i,la=Ve("Hours",!0);function da(e,t,r){return e>11?r?"pm":"PM":r?"am":"AM"}var Jn={calendar:Ks,longDateFormat:ti,invalidDate:ni,ordinal:ii,dayOfMonthOrdinalParse:ai,relativeTime:li,months:Ti,monthsShort:Bn,week:Li,weekdays:zi,weekdaysMin:Hi,weekdaysShort:qn,meridiemParse:oa},z={},Ze={},tt;function ca(e,t){var r,n=Math.min(e.length,t.length);for(r=0;r<n;r+=1)if(e[r]!==t[r])return r;return n}function Sn(e){return e&&e.toLowerCase().replace("_","-")}function ua(e){for(var t=0,r,n,s,i;t<e.length;){for(i=Sn(e[t]).split("-"),r=i.length,n=Sn(e[t+1]),n=n?n.split("-"):null;r>0;){if(s=Et(i.slice(0,r).join("-")),s)return s;if(n&&n.length>=r&&ca(i,n)>=r-1)break;r--}t++}return tt}function ha(e){return!!(e&&e.match("^[^/\\\\]*$"))}function Et(e){var t=null,r;if(z[e]===void 0&&typeof module<"u"&&module&&module.exports&&ha(e))try{t=tt._abbr,r=require,r("./locale/"+e),Ce(t)}catch{z[e]=null}return z[e]}function Ce(e,t){var r;return e&&(q(t)?r=Se(e):r=Nr(e,t),r?tt=r:typeof console<"u"&&console.warn&&console.warn("Locale "+e+" not found. Did you forget to load it?")),tt._abbr}function Nr(e,t){if(t!==null){var r,n=Jn;if(t.abbr=e,z[e]!=null)Fn("defineLocaleOverride","use moment.updateLocale(localeName, config) to change an existing locale. moment.defineLocale(localeName, config) should only be used for creating a new locale See http://momentjs.com/guides/#/warnings/define-locale/ for more info."),n=z[e]._config;else if(t.parentLocale!=null)if(z[t.parentLocale]!=null)n=z[t.parentLocale]._config;else if(r=Et(t.parentLocale),r!=null)n=r._config;else return Ze[t.parentLocale]||(Ze[t.parentLocale]=[]),Ze[t.parentLocale].push({name:e,config:t}),null;return z[e]=new _r(cr(n,t)),Ze[e]&&Ze[e].forEach(function(s){Nr(s.name,s.config)}),Ce(e),z[e]}else return delete z[e],null}function fa(e,t){if(t!=null){var r,n,s=Jn;z[e]!=null&&z[e].parentLocale!=null?z[e].set(cr(z[e]._config,t)):(n=Et(e),n!=null&&(s=n._config),t=cr(s,t),n==null&&(t.abbr=e),r=new _r(t),r.parentLocale=z[e],z[e]=r),Ce(e)}else z[e]!=null&&(z[e].parentLocale!=null?(z[e]=z[e].parentLocale,e===Ce()&&Ce(e)):z[e]!=null&&delete z[e]);return z[e]}function Se(e){var t;if(e&&e._locale&&e._locale._abbr&&(e=e._locale._abbr),!e)return tt;if(!te(e)){if(t=Et(e),t)return t;e=[e]}return ua(e)}function ma(){return ur(z)}function Or(e){var t,r=e._a;return r&&R(e).overflow===-2&&(t=r[pe]<0||r[pe]>11?pe:r[oe]<1||r[oe]>kr(r[j],r[pe])?oe:r[V]<0||r[V]>24||r[V]===24&&(r[ee]!==0||r[ve]!==0||r[Ne]!==0)?V:r[ee]<0||r[ee]>59?ee:r[ve]<0||r[ve]>59?ve:r[Ne]<0||r[Ne]>999?Ne:-1,R(e)._overflowDayOfYear&&(t<j||t>oe)&&(t=oe),R(e)._overflowWeeks&&t===-1&&(t=vi),R(e)._overflowWeekday&&t===-1&&(t=wi),R(e).overflow=t),e}var ga=/^\s*((?:[+-]\d{6}|\d{4})-(?:\d\d-\d\d|W\d\d-\d|W\d\d|\d\d\d|\d\d))(?:(T| )(\d\d(?::\d\d(?::\d\d(?:[.,]\d+)?)?)?)([+-]\d\d(?::?\d\d)?|\s*Z)?)?$/,ya=/^\s*((?:[+-]\d{6}|\d{4})(?:\d\d\d\d|W\d\d\d|W\d\d|\d\d\d|\d\d|))(?:(T| )(\d\d(?:\d\d(?:\d\d(?:[.,]\d+)?)?)?)([+-]\d\d(?::?\d\d)?|\s*Z)?)?$/,pa=/Z|[+-]\d\d(?::?\d\d)?/,ht=[["YYYYYY-MM-DD",/[+-]\d{6}-\d\d-\d\d/],["YYYY-MM-DD",/\d{4}-\d\d-\d\d/],["GGGG-[W]WW-E",/\d{4}-W\d\d-\d/],["GGGG-[W]WW",/\d{4}-W\d\d/,!1],["YYYY-DDD",/\d{4}-\d{3}/],["YYYY-MM",/\d{4}-\d\d/,!1],["YYYYYYMMDD",/[+-]\d{10}/],["YYYYMMDD",/\d{8}/],["GGGG[W]WWE",/\d{4}W\d{3}/],["GGGG[W]WW",/\d{4}W\d{2}/,!1],["YYYYDDD",/\d{7}/],["YYYYMM",/\d{6}/,!1],["YYYY",/\d{4}/,!1]],or=[["HH:mm:ss.SSSS",/\d\d:\d\d:\d\d\.\d+/],["HH:mm:ss,SSSS",/\d\d:\d\d:\d\d,\d+/],["HH:mm:ss",/\d\d:\d\d:\d\d/],["HH:mm",/\d\d:\d\d/],["HHmmss.SSSS",/\d\d\d\d\d\d\.\d+/],["HHmmss,SSSS",/\d\d\d\d\d\d,\d+/],["HHmmss",/\d\d\d\d\d\d/],["HHmm",/\d\d\d\d/],["HH",/\d\d/]],va=/^\/?Date\((-?\d+)/i,wa=/^(?:(Mon|Tue|Wed|Thu|Fri|Sat|Sun),?\s)?(\d{1,2})\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s(\d{2,4})\s(\d\d):(\d\d)(?::(\d\d))?\s(?:(UT|GMT|[ECMP][SD]T)|([Zz])|([+-]\d{4}))$/,_a={UT:0,GMT:0,EDT:-240,EST:-300,CDT:-300,CST:-360,MDT:-360,MST:-420,PDT:-420,PST:-480};function Kn(e){var t,r,n=e._i,s=ga.exec(n)||ya.exec(n),i,a,l,o,d=ht.length,c=or.length;if(s){for(R(e).iso=!0,t=0,r=d;t<r;t++)if(ht[t][1].exec(s[1])){a=ht[t][0],i=ht[t][2]!==!1;break}if(a==null){e._isValid=!1;return}if(s[3]){for(t=0,r=c;t<r;t++)if(or[t][1].exec(s[3])){l=(s[2]||" ")+or[t][0];break}if(l==null){e._isValid=!1;return}}if(!i&&l!=null){e._isValid=!1;return}if(s[4])if(pa.exec(s[4]))o="Z";else{e._isValid=!1;return}e._f=a+(l||"")+(o||""),Fr(e)}else e._isValid=!1}function ba(e,t,r,n,s,i){var a=[Sa(e),Bn.indexOf(t),parseInt(r,10),parseInt(n,10),parseInt(s,10)];return i&&a.push(parseInt(i,10)),a}function Sa(e){var t=parseInt(e,10);return t<=49?2e3+t:t<=999?1900+t:t}function Ma(e){return e.replace(/\([^()]*\)|[\n\t]/g," ").replace(/(\s\s+)/g," ").replace(/^\s\s*/,"").replace(/\s\s*$/,"")}function Ta(e,t,r){if(e){var n=qn.indexOf(e),s=new Date(t[0],t[1],t[2]).getDay();if(n!==s)return R(r).weekdayMismatch=!0,r._isValid=!1,!1}return!0}function Da(e,t,r){if(e)return _a[e];if(t)return 0;var n=parseInt(r,10),s=n%100,i=(n-s)/100;return i*60+s}function Qn(e){var t=wa.exec(Ma(e._i)),r;if(t){if(r=ba(t[4],t[3],t[2],t[5],t[6],t[7]),!Ta(t[1],r,e))return;e._a=r,e._tzm=Da(t[8],t[9],t[10]),e._d=Xe.apply(null,e._a),e._d.setUTCMinutes(e._d.getUTCMinutes()-e._tzm),R(e).rfc2822=!0}else e._isValid=!1}function ka(e){var t=va.exec(e._i);if(t!==null){e._d=new Date(+t[1]);return}if(Kn(e),e._isValid===!1)delete e._isValid;else return;if(Qn(e),e._isValid===!1)delete e._isValid;else return;e._strict?e._isValid=!1:y.createFromInputFallback(e)}y.createFromInputFallback=K("value provided is not in a recognized RFC2822 or ISO format. moment construction falls back to js Date(), which is not reliable across all browsers and versions. Non RFC2822/ISO date formats are discouraged. Please refer to http://momentjs.com/guides/#/warnings/js-date/ for more info.",function(e){e._d=new Date(e._i+(e._useUTC?" UTC":""))});function Ie(e,t,r){return e??t??r}function Ca(e){var t=new Date(y.now());return e._useUTC?[t.getUTCFullYear(),t.getUTCMonth(),t.getUTCDate()]:[t.getFullYear(),t.getMonth(),t.getDate()]}function Ar(e){var t,r,n=[],s,i,a;if(!e._d){for(s=Ca(e),e._w&&e._a[oe]==null&&e._a[pe]==null&&Ea(e),e._dayOfYear!=null&&(a=Ie(e._a[j],s[j]),(e._dayOfYear>Ke(a)||e._dayOfYear===0)&&(R(e)._overflowDayOfYear=!0),r=Xe(a,0,e._dayOfYear),e._a[pe]=r.getUTCMonth(),e._a[oe]=r.getUTCDate()),t=0;t<3&&e._a[t]==null;++t)e._a[t]=n[t]=s[t];for(;t<7;t++)e._a[t]=n[t]=e._a[t]==null?t===2?1:0:e._a[t];e._a[V]===24&&e._a[ee]===0&&e._a[ve]===0&&e._a[Ne]===0&&(e._nextDay=!0,e._a[V]=0),e._d=(e._useUTC?Xe:Ri).apply(null,n),i=e._useUTC?e._d.getUTCDay():e._d.getDay(),e._tzm!=null&&e._d.setUTCMinutes(e._d.getUTCMinutes()-e._tzm),e._nextDay&&(e._a[V]=24),e._w&&typeof e._w.d<"u"&&e._w.d!==i&&(R(e).weekdayMismatch=!0)}}function Ea(e){var t,r,n,s,i,a,l,o,d;t=e._w,t.GG!=null||t.W!=null||t.E!=null?(i=1,a=4,r=Ie(t.GG,e._a[j],et(B(),1,4).year),n=Ie(t.W,1),s=Ie(t.E,1),(s<1||s>7)&&(o=!0)):(i=e._locale._week.dow,a=e._locale._week.doy,d=et(B(),i,a),r=Ie(t.gg,e._a[j],d.year),n=Ie(t.w,d.week),t.d!=null?(s=t.d,(s<0||s>6)&&(o=!0)):t.e!=null?(s=t.e+i,(t.e<0||t.e>6)&&(o=!0)):s=i),n<1||n>_e(r,i,a)?R(e)._overflowWeeks=!0:o!=null?R(e)._overflowWeekday=!0:(l=jn(r,n,s,i,a),e._a[j]=l.year,e._dayOfYear=l.dayOfYear)}y.ISO_8601=function(){};y.RFC_2822=function(){};function Fr(e){if(e._f===y.ISO_8601){Kn(e);return}if(e._f===y.RFC_2822){Qn(e);return}e._a=[],R(e).empty=!0;var t=""+e._i,r,n,s,i,a,l=t.length,o=0,d,c;for(s=Rn(e._f,e._locale).match(br)||[],c=s.length,r=0;r<c;r++)i=s[r],n=(t.match(gi(i,e))||[])[0],n&&(a=t.substr(0,t.indexOf(n)),a.length>0&&R(e).unusedInput.push(a),t=t.slice(t.indexOf(n)+n.length),o+=n.length),Ue[i]?(n?R(e).empty=!1:R(e).unusedTokens.push(i),pi(i,n,e)):e._strict&&!n&&R(e).unusedTokens.push(i);R(e).charsLeftOver=l-o,t.length>0&&R(e).unusedInput.push(t),e._a[V]<=12&&R(e).bigHour===!0&&e._a[V]>0&&(R(e).bigHour=void 0),R(e).parsedDateParts=e._a.slice(0),R(e).meridiem=e._meridiem,e._a[V]=xa(e._locale,e._a[V],e._meridiem),d=R(e).era,d!==null&&(e._a[j]=e._locale.erasConvertYear(d,e._a[j])),Ar(e),Or(e)}function xa(e,t,r){var n;return r==null?t:e.meridiemHour!=null?e.meridiemHour(t,r):(e.isPM!=null&&(n=e.isPM(r),n&&t<12&&(t+=12),!n&&t===12&&(t=0)),t)}function Na(e){var t,r,n,s,i,a,l=!1,o=e._f.length;if(o===0){R(e).invalidFormat=!0,e._d=new Date(NaN);return}for(s=0;s<o;s++)i=0,a=!1,t=wr({},e),e._useUTC!=null&&(t._useUTC=e._useUTC),t._f=e._f[s],Fr(t),vr(t)&&(a=!0),i+=R(t).charsLeftOver,i+=R(t).unusedTokens.length*10,R(t).score=i,l?i<n&&(n=i,r=t):(n==null||i<n||a)&&(n=i,r=t,a&&(l=!0));De(e,r||t)}function Oa(e){if(!e._d){var t=Sr(e._i),r=t.day===void 0?t.date:t.day;e._a=On([t.year,t.month,r,t.hour,t.minute,t.second,t.millisecond],function(n){return n&&parseInt(n,10)}),Ar(e)}}function Aa(e){var t=new nt(Or(Xn(e)));return t._nextDay&&(t.add(1,"d"),t._nextDay=void 0),t}function Xn(e){var t=e._i,r=e._f;return e._locale=e._locale||Se(e._l),t===null||r===void 0&&t===""?bt({nullInput:!0}):(typeof t=="string"&&(e._i=t=e._locale.preparse(t)),re(t)?new nt(Or(t)):(rt(t)?e._d=t:te(r)?Na(e):r?Fr(e):Fa(e),vr(e)||(e._d=null),e))}function Fa(e){var t=e._i;q(t)?e._d=new Date(y.now()):rt(t)?e._d=new Date(t.valueOf()):typeof t=="string"?ka(e):te(t)?(e._a=On(t.slice(0),function(r){return parseInt(r,10)}),Ar(e)):Oe(t)?Oa(e):be(t)?e._d=new Date(t):y.createFromInputFallback(e)}function es(e,t,r,n,s){var i={};return(t===!0||t===!1)&&(n=t,t=void 0),(r===!0||r===!1)&&(n=r,r=void 0),(Oe(e)&&pr(e)||te(e)&&e.length===0)&&(e=void 0),i._isAMomentObject=!0,i._useUTC=i._isUTC=s,i._l=r,i._i=e,i._f=t,i._strict=n,Aa(i)}function B(e,t,r,n){return es(e,t,r,n,!1)}var Ra=K("moment().min is deprecated, use moment.max instead. http://momentjs.com/guides/#/warnings/min-max/",function(){var e=B.apply(null,arguments);return this.isValid()&&e.isValid()?e<this?this:e:bt()}),Pa=K("moment().max is deprecated, use moment.min instead. http://momentjs.com/guides/#/warnings/min-max/",function(){var e=B.apply(null,arguments);return this.isValid()&&e.isValid()?e>this?this:e:bt()});function ts(e,t){var r,n;if(t.length===1&&te(t[0])&&(t=t[0]),!t.length)return B();for(r=t[0],n=1;n<t.length;++n)(!t[n].isValid()||t[n][e](r))&&(r=t[n]);return r}function La(){var e=[].slice.call(arguments,0);return ts("isBefore",e)}function Ya(){var e=[].slice.call(arguments,0);return ts("isAfter",e)}var Ia=function(){return Date.now?Date.now():+new Date},Je=["year","quarter","month","week","day","hour","minute","second","millisecond"];function $a(e){var t,r=!1,n,s=Je.length;for(t in e)if(Y(e,t)&&!(H.call(Je,t)!==-1&&(e[t]==null||!isNaN(e[t]))))return!1;for(n=0;n<s;++n)if(e[Je[n]]){if(r)return!1;parseFloat(e[Je[n]])!==P(e[Je[n]])&&(r=!0)}return!0}function Ua(){return this._isValid}function Ba(){return ne(NaN)}function xt(e){var t=Sr(e),r=t.year||0,n=t.quarter||0,s=t.month||0,i=t.week||t.isoWeek||0,a=t.day||0,l=t.hour||0,o=t.minute||0,d=t.second||0,c=t.millisecond||0;this._isValid=$a(t),this._milliseconds=+c+d*1e3+o*6e4+l*1e3*60*60,this._days=+a+i*7,this._months=+s+n*3+r*12,this._data={},this._locale=Se(),this._bubble()}function mt(e){return e instanceof xt}function fr(e){return e<0?Math.round(-1*e)*-1:Math.round(e)}function Wa(e,t,r){var n=Math.min(e.length,t.length),s=Math.abs(e.length-t.length),i=0,a;for(a=0;a<n;a++)P(e[a])!==P(t[a])&&i++;return i+s}function rs(e,t){D(e,0,0,function(){var r=this.utcOffset(),n="+";return r<0&&(r=-r,n="-"),n+le(~~(r/60),2)+t+le(~~r%60,2)})}rs("Z",":");rs("ZZ","");w("Z",kt);w("ZZ",kt);$(["Z","ZZ"],function(e,t,r){r._useUTC=!0,r._tzm=Rr(kt,e)});var za=/([\+\-]|\d\d)/gi;function Rr(e,t){var r=(t||"").match(e),n,s,i;return r===null?null:(n=r[r.length-1]||[],s=(n+"").match(za)||["-",0,0],i=+(s[1]*60)+P(s[2]),i===0?0:s[0]==="+"?i:-i)}function Pr(e,t){var r,n;return t._isUTC?(r=t.clone(),n=(re(e)||rt(e)?e.valueOf():B(e).valueOf())-r.valueOf(),r._d.setTime(r._d.valueOf()+n),y.updateOffset(r,!1),r):B(e).local()}function mr(e){return-Math.round(e._d.getTimezoneOffset())}y.updateOffset=function(){};function Ha(e,t,r){var n=this._offset||0,s;if(!this.isValid())return e!=null?this:NaN;if(e!=null){if(typeof e=="string"){if(e=Rr(kt,e),e===null)return this}else Math.abs(e)<16&&!r&&(e=e*60);return!this._isUTC&&t&&(s=mr(this)),this._offset=e,this._isUTC=!0,s!=null&&this.add(s,"m"),n!==e&&(!t||this._changeInProgress?is(this,ne(e-n,"m"),1,!1):this._changeInProgress||(this._changeInProgress=!0,y.updateOffset(this,!0),this._changeInProgress=null)),this}else return this._isUTC?n:mr(this)}function Va(e,t){return e!=null?(typeof e!="string"&&(e=-e),this.utcOffset(e,t),this):-this.utcOffset()}function ja(e){return this.utcOffset(0,e)}function qa(e){return this._isUTC&&(this.utcOffset(0,e),this._isUTC=!1,e&&this.subtract(mr(this),"m")),this}function Ga(){if(this._tzm!=null)this.utcOffset(this._tzm,!1,!0);else if(typeof this._i=="string"){var e=Rr(fi,this._i);e!=null?this.utcOffset(e):this.utcOffset(0,!0)}return this}function Za(e){return this.isValid()?(e=e?B(e).utcOffset():0,(this.utcOffset()-e)%60===0):!1}function Ja(){return this.utcOffset()>this.clone().month(0).utcOffset()||this.utcOffset()>this.clone().month(5).utcOffset()}function Ka(){if(!q(this._isDSTShifted))return this._isDSTShifted;var e={},t;return wr(e,this),e=Xn(e),e._a?(t=e._isUTC?de(e._a):B(e._a),this._isDSTShifted=this.isValid()&&Wa(e._a,t.toArray())>0):this._isDSTShifted=!1,this._isDSTShifted}function Qa(){return this.isValid()?!this._isUTC:!1}function Xa(){return this.isValid()?this._isUTC:!1}function ns(){return this.isValid()?this._isUTC&&this._offset===0:!1}var eo=/^(-|\+)?(?:(\d*)[. ])?(\d+):(\d+)(?::(\d+)(\.\d*)?)?$/,to=/^(-|\+)?P(?:([-+]?[0-9,.]*)Y)?(?:([-+]?[0-9,.]*)M)?(?:([-+]?[0-9,.]*)W)?(?:([-+]?[0-9,.]*)D)?(?:T(?:([-+]?[0-9,.]*)H)?(?:([-+]?[0-9,.]*)M)?(?:([-+]?[0-9,.]*)S)?)?$/;function ne(e,t){var r=e,n=null,s,i,a;return mt(e)?r={ms:e._milliseconds,d:e._days,M:e._months}:be(e)||!isNaN(+e)?(r={},t?r[t]=+e:r.milliseconds=+e):(n=eo.exec(e))?(s=n[1]==="-"?-1:1,r={y:0,d:P(n[oe])*s,h:P(n[V])*s,m:P(n[ee])*s,s:P(n[ve])*s,ms:P(fr(n[Ne]*1e3))*s}):(n=to.exec(e))?(s=n[1]==="-"?-1:1,r={y:xe(n[2],s),M:xe(n[3],s),w:xe(n[4],s),d:xe(n[5],s),h:xe(n[6],s),m:xe(n[7],s),s:xe(n[8],s)}):r==null?r={}:typeof r=="object"&&("from"in r||"to"in r)&&(a=ro(B(r.from),B(r.to)),r={},r.ms=a.milliseconds,r.M=a.months),i=new xt(r),mt(e)&&Y(e,"_locale")&&(i._locale=e._locale),mt(e)&&Y(e,"_isValid")&&(i._isValid=e._isValid),i}ne.fn=xt.prototype;ne.invalid=Ba;function xe(e,t){var r=e&&parseFloat(e.replace(",","."));return(isNaN(r)?0:r)*t}function Mn(e,t){var r={};return r.months=t.month()-e.month()+(t.year()-e.year())*12,e.clone().add(r.months,"M").isAfter(t)&&--r.months,r.milliseconds=+t-+e.clone().add(r.months,"M"),r}function ro(e,t){var r;return e.isValid()&&t.isValid()?(t=Pr(t,e),e.isBefore(t)?r=Mn(e,t):(r=Mn(t,e),r.milliseconds=-r.milliseconds,r.months=-r.months),r):{milliseconds:0,months:0}}function ss(e,t){return function(r,n){var s,i;return n!==null&&!isNaN(+n)&&(Fn(t,"moment()."+t+"(period, number) is deprecated. Please use moment()."+t+"(number, period). See http://momentjs.com/guides/#/warnings/add-inverted-param/ for more info."),i=r,r=n,n=i),s=ne(r,n),is(this,s,e),this}}function is(e,t,r,n){var s=t._milliseconds,i=fr(t._days),a=fr(t._months);e.isValid()&&(n=n??!0,a&&zn(e,Qe(e,"Month")+a*r),i&&Un(e,"Date",Qe(e,"Date")+i*r),s&&e._d.setTime(e._d.valueOf()+s*r),n&&y.updateOffset(e,i||a))}var no=ss(1,"add"),so=ss(-1,"subtract");function as(e){return typeof e=="string"||e instanceof String}function io(e){return re(e)||rt(e)||as(e)||be(e)||oo(e)||ao(e)||e===null||e===void 0}function ao(e){var t=Oe(e)&&!pr(e),r=!1,n=["years","year","y","months","month","M","days","day","d","dates","date","D","hours","hour","h","minutes","minute","m","seconds","second","s","milliseconds","millisecond","ms"],s,i,a=n.length;for(s=0;s<a;s+=1)i=n[s],r=r||Y(e,i);return t&&r}function oo(e){var t=te(e),r=!1;return t&&(r=e.filter(function(n){return!be(n)&&as(e)}).length===0),t&&r}function lo(e){var t=Oe(e)&&!pr(e),r=!1,n=["sameDay","nextDay","lastDay","nextWeek","lastWeek","sameElse"],s,i;for(s=0;s<n.length;s+=1)i=n[s],r=r||Y(e,i);return t&&r}function co(e,t){var r=e.diff(t,"days",!0);return r<-6?"sameElse":r<-1?"lastWeek":r<0?"lastDay":r<1?"sameDay":r<2?"nextDay":r<7?"nextWeek":"sameElse"}function uo(e,t){arguments.length===1&&(arguments[0]?io(arguments[0])?(e=arguments[0],t=void 0):lo(arguments[0])&&(t=arguments[0],e=void 0):(e=void 0,t=void 0));var r=e||B(),n=Pr(r,this).startOf("day"),s=y.calendarFormat(this,n)||"sameElse",i=t&&(ce(t[s])?t[s].call(this,r):t[s]);return this.format(i||this.localeData().calendar(s,this,B(r)))}function ho(){return new nt(this)}function fo(e,t){var r=re(e)?e:B(e);return this.isValid()&&r.isValid()?(t=Q(t)||"millisecond",t==="millisecond"?this.valueOf()>r.valueOf():r.valueOf()<this.clone().startOf(t).valueOf()):!1}function mo(e,t){var r=re(e)?e:B(e);return this.isValid()&&r.isValid()?(t=Q(t)||"millisecond",t==="millisecond"?this.valueOf()<r.valueOf():this.clone().endOf(t).valueOf()<r.valueOf()):!1}function go(e,t,r,n){var s=re(e)?e:B(e),i=re(t)?t:B(t);return this.isValid()&&s.isValid()&&i.isValid()?(n=n||"()",(n[0]==="("?this.isAfter(s,r):!this.isBefore(s,r))&&(n[1]===")"?this.isBefore(i,r):!this.isAfter(i,r))):!1}function yo(e,t){var r=re(e)?e:B(e),n;return this.isValid()&&r.isValid()?(t=Q(t)||"millisecond",t==="millisecond"?this.valueOf()===r.valueOf():(n=r.valueOf(),this.clone().startOf(t).valueOf()<=n&&n<=this.clone().endOf(t).valueOf())):!1}function po(e,t){return this.isSame(e,t)||this.isAfter(e,t)}function vo(e,t){return this.isSame(e,t)||this.isBefore(e,t)}function wo(e,t,r){var n,s,i;if(!this.isValid())return NaN;if(n=Pr(e,this),!n.isValid())return NaN;switch(s=(n.utcOffset()-this.utcOffset())*6e4,t=Q(t),t){case"year":i=gt(this,n)/12;break;case"month":i=gt(this,n);break;case"quarter":i=gt(this,n)/3;break;case"second":i=(this-n)/1e3;break;case"minute":i=(this-n)/6e4;break;case"hour":i=(this-n)/36e5;break;case"day":i=(this-n-s)/864e5;break;case"week":i=(this-n-s)/6048e5;break;default:i=this-n}return r?i:J(i)}function gt(e,t){if(e.date()<t.date())return-gt(t,e);var r=(t.year()-e.year())*12+(t.month()-e.month()),n=e.clone().add(r,"months"),s,i;return t-n<0?(s=e.clone().add(r-1,"months"),i=(t-n)/(n-s)):(s=e.clone().add(r+1,"months"),i=(t-n)/(s-n)),-(r+i)||0}y.defaultFormat="YYYY-MM-DDTHH:mm:ssZ";y.defaultFormatUtc="YYYY-MM-DDTHH:mm:ss[Z]";function _o(){return this.clone().locale("en").format("ddd MMM DD YYYY HH:mm:ss [GMT]ZZ")}function bo(e){if(!this.isValid())return null;var t=e!==!0,r=t?this.clone().utc():this;return r.year()<0||r.year()>9999?ft(r,t?"YYYYYY-MM-DD[T]HH:mm:ss.SSS[Z]":"YYYYYY-MM-DD[T]HH:mm:ss.SSSZ"):ce(Date.prototype.toISOString)?t?this.toDate().toISOString():new Date(this.valueOf()+this.utcOffset()*60*1e3).toISOString().replace("Z",ft(r,"Z")):ft(r,t?"YYYY-MM-DD[T]HH:mm:ss.SSS[Z]":"YYYY-MM-DD[T]HH:mm:ss.SSSZ")}function So(){if(!this.isValid())return"moment.invalid(/* "+this._i+" */)";var e="moment",t="",r,n,s,i;return this.isLocal()||(e=this.utcOffset()===0?"moment.utc":"moment.parseZone",t="Z"),r="["+e+'("]',n=0<=this.year()&&this.year()<=9999?"YYYY":"YYYYYY",s="-MM-DD[T]HH:mm:ss.SSS",i=t+'[")]',this.format(r+n+s+i)}function Mo(e){e||(e=this.isUtc()?y.defaultFormatUtc:y.defaultFormat);var t=ft(this,e);return this.localeData().postformat(t)}function To(e,t){return this.isValid()&&(re(e)&&e.isValid()||B(e).isValid())?ne({to:this,from:e}).locale(this.locale()).humanize(!t):this.localeData().invalidDate()}function Do(e){return this.from(B(),e)}function ko(e,t){return this.isValid()&&(re(e)&&e.isValid()||B(e).isValid())?ne({from:this,to:e}).locale(this.locale()).humanize(!t):this.localeData().invalidDate()}function Co(e){return this.to(B(),e)}function os(e){var t;return e===void 0?this._locale._abbr:(t=Se(e),t!=null&&(this._locale=t),this)}var ls=K("moment().lang() is deprecated. Instead, use moment().localeData() to get the language configuration. Use moment().locale() to change languages.",function(e){return e===void 0?this.localeData():this.locale(e)});function ds(){return this._locale}var vt=1e3,Be=60*vt,wt=60*Be,cs=(365*400+97)*24*wt;function We(e,t){return(e%t+t)%t}function us(e,t,r){return e<100&&e>=0?new Date(e+400,t,r)-cs:new Date(e,t,r).valueOf()}function hs(e,t,r){return e<100&&e>=0?Date.UTC(e+400,t,r)-cs:Date.UTC(e,t,r)}function Eo(e){var t,r;if(e=Q(e),e===void 0||e==="millisecond"||!this.isValid())return this;switch(r=this._isUTC?hs:us,e){case"year":t=r(this.year(),0,1);break;case"quarter":t=r(this.year(),this.month()-this.month()%3,1);break;case"month":t=r(this.year(),this.month(),1);break;case"week":t=r(this.year(),this.month(),this.date()-this.weekday());break;case"isoWeek":t=r(this.year(),this.month(),this.date()-(this.isoWeekday()-1));break;case"day":case"date":t=r(this.year(),this.month(),this.date());break;case"hour":t=this._d.valueOf(),t-=We(t+(this._isUTC?0:this.utcOffset()*Be),wt);break;case"minute":t=this._d.valueOf(),t-=We(t,Be);break;case"second":t=this._d.valueOf(),t-=We(t,vt);break}return this._d.setTime(t),y.updateOffset(this,!0),this}function xo(e){var t,r;if(e=Q(e),e===void 0||e==="millisecond"||!this.isValid())return this;switch(r=this._isUTC?hs:us,e){case"year":t=r(this.year()+1,0,1)-1;break;case"quarter":t=r(this.year(),this.month()-this.month()%3+3,1)-1;break;case"month":t=r(this.year(),this.month()+1,1)-1;break;case"week":t=r(this.year(),this.month(),this.date()-this.weekday()+7)-1;break;case"isoWeek":t=r(this.year(),this.month(),this.date()-(this.isoWeekday()-1)+7)-1;break;case"day":case"date":t=r(this.year(),this.month(),this.date()+1)-1;break;case"hour":t=this._d.valueOf(),t+=wt-We(t+(this._isUTC?0:this.utcOffset()*Be),wt)-1;break;case"minute":t=this._d.valueOf(),t+=Be-We(t,Be)-1;break;case"second":t=this._d.valueOf(),t+=vt-We(t,vt)-1;break}return this._d.setTime(t),y.updateOffset(this,!0),this}function No(){return this._d.valueOf()-(this._offset||0)*6e4}function Oo(){return Math.floor(this.valueOf()/1e3)}function Ao(){return new Date(this.valueOf())}function Fo(){var e=this;return[e.year(),e.month(),e.date(),e.hour(),e.minute(),e.second(),e.millisecond()]}function Ro(){var e=this;return{years:e.year(),months:e.month(),date:e.date(),hours:e.hours(),minutes:e.minutes(),seconds:e.seconds(),milliseconds:e.milliseconds()}}function Po(){return this.isValid()?this.toISOString():null}function Lo(){return vr(this)}function Yo(){return De({},R(this))}function Io(){return R(this).overflow}function $o(){return{input:this._i,format:this._f,locale:this._locale,isUTC:this._isUTC,strict:this._strict}}D("N",0,0,"eraAbbr");D("NN",0,0,"eraAbbr");D("NNN",0,0,"eraAbbr");D("NNNN",0,0,"eraName");D("NNNNN",0,0,"eraNarrow");D("y",["y",1],"yo","eraYear");D("y",["yy",2],0,"eraYear");D("y",["yyy",3],0,"eraYear");D("y",["yyyy",4],0,"eraYear");w("N",Lr);w("NN",Lr);w("NNN",Lr);w("NNNN",Jo);w("NNNNN",Ko);$(["N","NN","NNN","NNNN","NNNNN"],function(e,t,r,n){var s=r._locale.erasParse(e,n,r._strict);s?R(r).era=s:R(r).invalidEra=e});w("y",ze);w("yy",ze);w("yyy",ze);w("yyyy",ze);w("yo",Qo);$(["y","yy","yyy","yyyy"],j);$(["yo"],function(e,t,r,n){var s;r._locale._eraYearOrdinalRegex&&(s=e.match(r._locale._eraYearOrdinalRegex)),r._locale.eraYearOrdinalParse?t[j]=r._locale.eraYearOrdinalParse(e,s):t[j]=parseInt(e,10)});function Uo(e,t){var r,n,s,i=this._eras||Se("en")._eras;for(r=0,n=i.length;r<n;++r)switch(typeof i[r].since==="string"&&(s=y(i[r].since).startOf("day"),i[r].since=s.valueOf()),typeof i[r].until){case"undefined":i[r].until=1/0;break;case"string":s=y(i[r].until).startOf("day").valueOf(),i[r].until=s.valueOf();break}return i}function Bo(e,t,r){var n,s,i=this.eras(),a,l,o;for(e=e.toUpperCase(),n=0,s=i.length;n<s;++n)if(a=i[n].name.toUpperCase(),l=i[n].abbr.toUpperCase(),o=i[n].narrow.toUpperCase(),r)switch(t){case"N":case"NN":case"NNN":if(l===e)return i[n];break;case"NNNN":if(a===e)return i[n];break;case"NNNNN":if(o===e)return i[n];break}else if([a,l,o].indexOf(e)>=0)return i[n]}function Wo(e,t){var r=e.since<=e.until?1:-1;return t===void 0?y(e.since).year():y(e.since).year()+(t-e.offset)*r}function zo(){var e,t,r,n=this.localeData().eras();for(e=0,t=n.length;e<t;++e)if(r=this.clone().startOf("day").valueOf(),n[e].since<=r&&r<=n[e].until||n[e].until<=r&&r<=n[e].since)return n[e].name;return""}function Ho(){var e,t,r,n=this.localeData().eras();for(e=0,t=n.length;e<t;++e)if(r=this.clone().startOf("day").valueOf(),n[e].since<=r&&r<=n[e].until||n[e].until<=r&&r<=n[e].since)return n[e].narrow;return""}function Vo(){var e,t,r,n=this.localeData().eras();for(e=0,t=n.length;e<t;++e)if(r=this.clone().startOf("day").valueOf(),n[e].since<=r&&r<=n[e].until||n[e].until<=r&&r<=n[e].since)return n[e].abbr;return""}function jo(){var e,t,r,n,s=this.localeData().eras();for(e=0,t=s.length;e<t;++e)if(r=s[e].since<=s[e].until?1:-1,n=this.clone().startOf("day").valueOf(),s[e].since<=n&&n<=s[e].until||s[e].until<=n&&n<=s[e].since)return(this.year()-y(s[e].since).year())*r+s[e].offset;return this.year()}function qo(e){return Y(this,"_erasNameRegex")||Yr.call(this),e?this._erasNameRegex:this._erasRegex}function Go(e){return Y(this,"_erasAbbrRegex")||Yr.call(this),e?this._erasAbbrRegex:this._erasRegex}function Zo(e){return Y(this,"_erasNarrowRegex")||Yr.call(this),e?this._erasNarrowRegex:this._erasRegex}function Lr(e,t){return t.erasAbbrRegex(e)}function Jo(e,t){return t.erasNameRegex(e)}function Ko(e,t){return t.erasNarrowRegex(e)}function Qo(e,t){return t._eraYearOrdinalRegex||ze}function Yr(){var e=[],t=[],r=[],n=[],s,i,a,l,o,d=this.eras();for(s=0,i=d.length;s<i;++s)a=we(d[s].name),l=we(d[s].abbr),o=we(d[s].narrow),t.push(a),e.push(l),r.push(o),n.push(a),n.push(l),n.push(o);this._erasRegex=new RegExp("^("+n.join("|")+")","i"),this._erasNameRegex=new RegExp("^("+t.join("|")+")","i"),this._erasAbbrRegex=new RegExp("^("+e.join("|")+")","i"),this._erasNarrowRegex=new RegExp("^("+r.join("|")+")","i")}D(0,["gg",2],0,function(){return this.weekYear()%100});D(0,["GG",2],0,function(){return this.isoWeekYear()%100});function Nt(e,t){D(0,[e,e.length],0,t)}Nt("gggg","weekYear");Nt("ggggg","weekYear");Nt("GGGG","isoWeekYear");Nt("GGGGG","isoWeekYear");w("G",Dt);w("g",Dt);w("GG",W,G);w("gg",W,G);w("GGGG",Tr,Mr);w("gggg",Tr,Mr);w("GGGGG",Tt,St);w("ggggg",Tt,St);it(["gggg","ggggg","GGGG","GGGGG"],function(e,t,r,n){t[n.substr(0,2)]=P(e)});it(["gg","GG"],function(e,t,r,n){t[n]=y.parseTwoDigitYear(e)});function Xo(e){return fs.call(this,e,this.week(),this.weekday()+this.localeData()._week.dow,this.localeData()._week.dow,this.localeData()._week.doy)}function el(e){return fs.call(this,e,this.isoWeek(),this.isoWeekday(),1,4)}function tl(){return _e(this.year(),1,4)}function rl(){return _e(this.isoWeekYear(),1,4)}function nl(){var e=this.localeData()._week;return _e(this.year(),e.dow,e.doy)}function sl(){var e=this.localeData()._week;return _e(this.weekYear(),e.dow,e.doy)}function fs(e,t,r,n,s){var i;return e==null?et(this,n,s).year:(i=_e(e,n,s),t>i&&(t=i),il.call(this,e,t,r,n,s))}function il(e,t,r,n,s){var i=jn(e,t,r,n,s),a=Xe(i.year,0,i.dayOfYear);return this.year(a.getUTCFullYear()),this.month(a.getUTCMonth()),this.date(a.getUTCDate()),this}D("Q",0,"Qo","quarter");w("Q",Pn);$("Q",function(e,t){t[pe]=(P(e)-1)*3});function al(e){return e==null?Math.ceil((this.month()+1)/3):this.month((e-1)*3+this.month()%3)}D("D",["DD",2],"Do","date");w("D",W,He);w("DD",W,G);w("Do",function(e,t){return e?t._dayOfMonthOrdinalParse||t._ordinalParse:t._dayOfMonthOrdinalParseLenient});$(["D","DD"],oe);$("Do",function(e,t){t[oe]=P(e.match(W)[0])});var ms=Ve("Date",!0);D("DDD",["DDDD",3],"DDDo","dayOfYear");w("DDD",Mt);w("DDDD",Ln);$(["DDD","DDDD"],function(e,t,r){r._dayOfYear=P(e)});function ol(e){var t=Math.round((this.clone().startOf("day")-this.clone().startOf("year"))/864e5)+1;return e==null?t:this.add(e-t,"d")}D("m",["mm",2],0,"minute");w("m",W,Dr);w("mm",W,G);$(["m","mm"],ee);var ll=Ve("Minutes",!1);D("s",["ss",2],0,"second");w("s",W,Dr);w("ss",W,G);$(["s","ss"],ve);var dl=Ve("Seconds",!1);D("S",0,0,function(){return~~(this.millisecond()/100)});D(0,["SS",2],0,function(){return~~(this.millisecond()/10)});D(0,["SSS",3],0,"millisecond");D(0,["SSSS",4],0,function(){return this.millisecond()*10});D(0,["SSSSS",5],0,function(){return this.millisecond()*100});D(0,["SSSSSS",6],0,function(){return this.millisecond()*1e3});D(0,["SSSSSSS",7],0,function(){return this.millisecond()*1e4});D(0,["SSSSSSSS",8],0,function(){return this.millisecond()*1e5});D(0,["SSSSSSSSS",9],0,function(){return this.millisecond()*1e6});w("S",Mt,Pn);w("SS",Mt,G);w("SSS",Mt,Ln);var ke,gs;for(ke="SSSS";ke.length<=9;ke+="S")w(ke,ze);function cl(e,t){t[Ne]=P(("0."+e)*1e3)}for(ke="S";ke.length<=9;ke+="S")$(ke,cl);gs=Ve("Milliseconds",!1);D("z",0,0,"zoneAbbr");D("zz",0,0,"zoneName");function ul(){return this._isUTC?"UTC":""}function hl(){return this._isUTC?"Coordinated Universal Time":""}var m=nt.prototype;m.add=no;m.calendar=uo;m.clone=ho;m.diff=wo;m.endOf=xo;m.format=Mo;m.from=To;m.fromNow=Do;m.to=ko;m.toNow=Co;m.get=bi;m.invalidAt=Io;m.isAfter=fo;m.isBefore=mo;m.isBetween=go;m.isSame=yo;m.isSameOrAfter=po;m.isSameOrBefore=vo;m.isValid=Lo;m.lang=ls;m.locale=os;m.localeData=ds;m.max=Pa;m.min=Ra;m.parsingFlags=Yo;m.set=Si;m.startOf=Eo;m.subtract=so;m.toArray=Fo;m.toObject=Ro;m.toDate=Ao;m.toISOString=bo;m.inspect=So;typeof Symbol<"u"&&Symbol.for!=null&&(m[Symbol.for("nodejs.util.inspect.custom")]=function(){return"Moment<"+this.format()+">"});m.toJSON=Po;m.toString=_o;m.unix=Oo;m.valueOf=No;m.creationData=$o;m.eraName=zo;m.eraNarrow=Ho;m.eraAbbr=Vo;m.eraYear=jo;m.year=$n;m.isLeapYear=_i;m.weekYear=Xo;m.isoWeekYear=el;m.quarter=m.quarters=al;m.month=Hn;m.daysInMonth=Oi;m.week=m.weeks=$i;m.isoWeek=m.isoWeeks=Ui;m.weeksInYear=nl;m.weeksInWeekYear=sl;m.isoWeeksInYear=tl;m.isoWeeksInISOWeekYear=rl;m.date=ms;m.day=m.days=Xi;m.weekday=ea;m.isoWeekday=ta;m.dayOfYear=ol;m.hour=m.hours=la;m.minute=m.minutes=ll;m.second=m.seconds=dl;m.millisecond=m.milliseconds=gs;m.utcOffset=Ha;m.utc=ja;m.local=qa;m.parseZone=Ga;m.hasAlignedHourOffset=Za;m.isDST=Ja;m.isLocal=Qa;m.isUtcOffset=Xa;m.isUtc=ns;m.isUTC=ns;m.zoneAbbr=ul;m.zoneName=hl;m.dates=K("dates accessor is deprecated. Use date instead.",ms);m.months=K("months accessor is deprecated. Use month instead",Hn);m.years=K("years accessor is deprecated. Use year instead",$n);m.zone=K("moment().zone is deprecated, use moment().utcOffset instead. http://momentjs.com/guides/#/warnings/zone/",Va);m.isDSTShifted=K("isDSTShifted is deprecated. See http://momentjs.com/guides/#/warnings/dst-shifted/ for more information",Ka);function fl(e){return B(e*1e3)}function ml(){return B.apply(null,arguments).parseZone()}function ys(e){return e}var I=_r.prototype;I.calendar=Qs;I.longDateFormat=ri;I.invalidDate=si;I.ordinal=oi;I.preparse=ys;I.postformat=ys;I.relativeTime=di;I.pastFuture=ci;I.set=Js;I.eras=Uo;I.erasParse=Bo;I.erasConvertYear=Wo;I.erasAbbrRegex=Go;I.erasNameRegex=qo;I.erasNarrowRegex=Zo;I.months=Ci;I.monthsShort=Ei;I.monthsParse=Ni;I.monthsRegex=Fi;I.monthsShortRegex=Ai;I.week=Pi;I.firstDayOfYear=Ii;I.firstDayOfWeek=Yi;I.weekdays=Gi;I.weekdaysMin=Ji;I.weekdaysShort=Zi;I.weekdaysParse=Qi;I.weekdaysRegex=ra;I.weekdaysShortRegex=na;I.weekdaysMinRegex=sa;I.isPM=aa;I.meridiem=da;function _t(e,t,r,n){var s=Se(),i=de().set(n,t);return s[r](i,e)}function ps(e,t,r){if(be(e)&&(t=e,e=void 0),e=e||"",t!=null)return _t(e,t,r,"month");var n,s=[];for(n=0;n<12;n++)s[n]=_t(e,n,r,"month");return s}function Ir(e,t,r,n){typeof e=="boolean"?(be(t)&&(r=t,t=void 0),t=t||""):(t=e,r=t,e=!1,be(t)&&(r=t,t=void 0),t=t||"");var s=Se(),i=e?s._week.dow:0,a,l=[];if(r!=null)return _t(t,(r+i)%7,n,"day");for(a=0;a<7;a++)l[a]=_t(t,(a+i)%7,n,"day");return l}function gl(e,t){return ps(e,t,"months")}function yl(e,t){return ps(e,t,"monthsShort")}function pl(e,t,r){return Ir(e,t,r,"weekdays")}function vl(e,t,r){return Ir(e,t,r,"weekdaysShort")}function wl(e,t,r){return Ir(e,t,r,"weekdaysMin")}Ce("en",{eras:[{since:"0001-01-01",until:1/0,offset:1,name:"Anno Domini",narrow:"AD",abbr:"AD"},{since:"0000-12-31",until:-1/0,offset:1,name:"Before Christ",narrow:"BC",abbr:"BC"}],dayOfMonthOrdinalParse:/\d{1,2}(th|st|nd|rd)/,ordinal:function(e){var t=e%10,r=P(e%100/10)===1?"th":t===1?"st":t===2?"nd":t===3?"rd":"th";return e+r}});y.lang=K("moment.lang is deprecated. Use moment.locale instead.",Ce);y.langData=K("moment.langData is deprecated. Use moment.localeData instead.",Se);var ge=Math.abs;function _l(){var e=this._data;return this._milliseconds=ge(this._milliseconds),this._days=ge(this._days),this._months=ge(this._months),e.milliseconds=ge(e.milliseconds),e.seconds=ge(e.seconds),e.minutes=ge(e.minutes),e.hours=ge(e.hours),e.months=ge(e.months),e.years=ge(e.years),this}function vs(e,t,r,n){var s=ne(t,r);return e._milliseconds+=n*s._milliseconds,e._days+=n*s._days,e._months+=n*s._months,e._bubble()}function bl(e,t){return vs(this,e,t,1)}function Sl(e,t){return vs(this,e,t,-1)}function Tn(e){return e<0?Math.floor(e):Math.ceil(e)}function Ml(){var e=this._milliseconds,t=this._days,r=this._months,n=this._data,s,i,a,l,o;return e>=0&&t>=0&&r>=0||e<=0&&t<=0&&r<=0||(e+=Tn(gr(r)+t)*864e5,t=0,r=0),n.milliseconds=e%1e3,s=J(e/1e3),n.seconds=s%60,i=J(s/60),n.minutes=i%60,a=J(i/60),n.hours=a%24,t+=J(a/24),o=J(ws(t)),r+=o,t-=Tn(gr(o)),l=J(r/12),r%=12,n.days=t,n.months=r,n.years=l,this}function ws(e){return e*4800/146097}function gr(e){return e*146097/4800}function Tl(e){if(!this.isValid())return NaN;var t,r,n=this._milliseconds;if(e=Q(e),e==="month"||e==="quarter"||e==="year")switch(t=this._days+n/864e5,r=this._months+ws(t),e){case"month":return r;case"quarter":return r/3;case"year":return r/12}else switch(t=this._days+Math.round(gr(this._months)),e){case"week":return t/7+n/6048e5;case"day":return t+n/864e5;case"hour":return t*24+n/36e5;case"minute":return t*1440+n/6e4;case"second":return t*86400+n/1e3;case"millisecond":return Math.floor(t*864e5)+n;default:throw new Error("Unknown unit "+e)}}function Me(e){return function(){return this.as(e)}}var _s=Me("ms"),Dl=Me("s"),kl=Me("m"),Cl=Me("h"),El=Me("d"),xl=Me("w"),Nl=Me("M"),Ol=Me("Q"),Al=Me("y"),Fl=_s;function Rl(){return ne(this)}function Pl(e){return e=Q(e),this.isValid()?this[e+"s"]():NaN}function Re(e){return function(){return this.isValid()?this._data[e]:NaN}}var Ll=Re("milliseconds"),Yl=Re("seconds"),Il=Re("minutes"),$l=Re("hours"),Ul=Re("days"),Bl=Re("months"),Wl=Re("years");function zl(){return J(this.days()/7)}var ye=Math.round,$e={ss:44,s:45,m:45,h:22,d:26,w:null,M:11};function Hl(e,t,r,n,s){return s.relativeTime(t||1,!!r,e,n)}function Vl(e,t,r,n){var s=ne(e).abs(),i=ye(s.as("s")),a=ye(s.as("m")),l=ye(s.as("h")),o=ye(s.as("d")),d=ye(s.as("M")),c=ye(s.as("w")),u=ye(s.as("y")),h=i<=r.ss&&["s",i]||i<r.s&&["ss",i]||a<=1&&["m"]||a<r.m&&["mm",a]||l<=1&&["h"]||l<r.h&&["hh",l]||o<=1&&["d"]||o<r.d&&["dd",o];return r.w!=null&&(h=h||c<=1&&["w"]||c<r.w&&["ww",c]),h=h||d<=1&&["M"]||d<r.M&&["MM",d]||u<=1&&["y"]||["yy",u],h[2]=t,h[3]=+e>0,h[4]=n,Hl.apply(null,h)}function jl(e){return e===void 0?ye:typeof e=="function"?(ye=e,!0):!1}function ql(e,t){return $e[e]===void 0?!1:t===void 0?$e[e]:($e[e]=t,e==="s"&&($e.ss=t-1),!0)}function Gl(e,t){if(!this.isValid())return this.localeData().invalidDate();var r=!1,n=$e,s,i;return typeof e=="object"&&(t=e,e=!1),typeof e=="boolean"&&(r=e),typeof t=="object"&&(n=Object.assign({},$e,t),t.s!=null&&t.ss==null&&(n.ss=t.s-1)),s=this.localeData(),i=Vl(this,!r,n,s),r&&(i=s.pastFuture(+this,i)),s.postformat(i)}var lr=Math.abs;function Ye(e){return(e>0)-(e<0)||+e}function Ot(){if(!this.isValid())return this.localeData().invalidDate();var e=lr(this._milliseconds)/1e3,t=lr(this._days),r=lr(this._months),n,s,i,a,l=this.asSeconds(),o,d,c,u;return l?(n=J(e/60),s=J(n/60),e%=60,n%=60,i=J(r/12),r%=12,a=e?e.toFixed(3).replace(/\.?0+$/,""):"",o=l<0?"-":"",d=Ye(this._months)!==Ye(l)?"-":"",c=Ye(this._days)!==Ye(l)?"-":"",u=Ye(this._milliseconds)!==Ye(l)?"-":"",o+"P"+(i?d+i+"Y":"")+(r?d+r+"M":"")+(t?c+t+"D":"")+(s||n||e?"T":"")+(s?u+s+"H":"")+(n?u+n+"M":"")+(e?u+a+"S":"")):"P0D"}var L=xt.prototype;L.isValid=Ua;L.abs=_l;L.add=bl;L.subtract=Sl;L.as=Tl;L.asMilliseconds=_s;L.asSeconds=Dl;L.asMinutes=kl;L.asHours=Cl;L.asDays=El;L.asWeeks=xl;L.asMonths=Nl;L.asQuarters=Ol;L.asYears=Al;L.valueOf=Fl;L._bubble=Ml;L.clone=Rl;L.get=Pl;L.milliseconds=Ll;L.seconds=Yl;L.minutes=Il;L.hours=$l;L.days=Ul;L.weeks=zl;L.months=Bl;L.years=Wl;L.humanize=Gl;L.toISOString=Ot;L.toString=Ot;L.toJSON=Ot;L.locale=os;L.localeData=ds;L.toIsoString=K("toIsoString() is deprecated. Please use toISOString() instead (notice the capitals)",Ot);L.lang=ls;D("X",0,0,"unix");D("x",0,0,"valueOf");w("x",Dt);w("X",mi);$("X",function(e,t,r){r._d=new Date(parseFloat(e)*1e3)});$("x",function(e,t,r){r._d=new Date(P(e))});y.version="2.30.1";Gs(B);y.fn=m;y.min=La;y.max=Ya;y.now=Ia;y.utc=de;y.unix=fl;y.months=gl;y.isDate=rt;y.locale=Ce;y.invalid=bt;y.duration=ne;y.isMoment=re;y.weekdays=pl;y.parseZone=ml;y.localeData=Se;y.isDuration=mt;y.monthsShort=yl;y.weekdaysMin=wl;y.defineLocale=Nr;y.updateLocale=fa;y.locales=ma;y.weekdaysShort=vl;y.normalizeUnits=Q;y.relativeTimeRounding=jl;y.relativeTimeThreshold=ql;y.calendarFormat=co;y.prototype=m;y.HTML5_FMT={DATETIME_LOCAL:"YYYY-MM-DDTHH:mm",DATETIME_LOCAL_SECONDS:"YYYY-MM-DDTHH:mm:ss",DATETIME_LOCAL_MS:"YYYY-MM-DDTHH:mm:ss.SSS",DATE:"YYYY-MM-DD",TIME:"HH:mm",TIME_SECONDS:"HH:mm:ss",TIME_MS:"HH:mm:ss.SSS",WEEK:"GGGG-[W]WW",MONTH:"YYYY-MM"};class Kl{static numeroALetras(t){if(t=parseInt(t),isNaN(t)||t<0||t>1e6)return"Número fuera de rango";const r=["cero","uno","dos","tres","cuatro","cinco","seis","siete","ocho","nueve"],n=["","","veinte","treinta","cuarenta","cincuenta","sesenta","setenta","ochenta","noventa"],s={10:"diez",11:"once",12:"doce",13:"trece",14:"catorce",15:"quince",16:"dieciséis",17:"diecisiete",18:"dieciocho",19:"diecinueve"},i=["","cien","doscientos","trescientos","cuatrocientos","quinientos","seiscientos","setecientos","ochocientos","novecientos"];function a(u){if(u<10)return r[u];if(u>=10&&u<20)return s[u];if(u<100){const f=u%10;return`${n[Math.floor(u/10)]}${f>0?" y "+r[f]:""}`}if(u===100)return"cien";const h=u%100;return`${i[Math.floor(u/100)]}${h>0?" "+a(h):""}`}if(t===1e6)return"un millón";let l=Math.floor(t/1e3),o=t%1e3,d=l>0?l===1?"mil":`${a(l)} mil`:"",c=o>0?a(o):"";return(d+" "+c).trim()}static imprimirCaja(t){}static async factura(t){return new Promise(async(r,n)=>{try{const s=me.conversorNumerosALetras,i=new s,a=ie().env,l=v=>Number(v||0).toFixed(2),o=v=>(v??"").toString(),d=Number(t.total??t.montoTotal??0),c=t.numeroFactura??t.numero_factura??t.id??"—",u=t.fechaEmision??(t.fecha&&t.hora?`${t.fecha} ${t.hora}`:"—"),h=t.nombre??t?.cliente?.nombre??"SN",f=t.complemento??t?.cliente?.complemento??"",p=t.ci??t?.cliente?.ci??"0",b=t.cliente_id??t?.cliente?.id??"—",M=a?.puntoVenta??0,g=t.cuf??null,k=g?g.match(/.{1,20}/g).join("<br>"):null,E=g?"FACTURA<br>CON DERECHO A CRÉDITO FISCAL":"NOTA DE VENTA",_=t.leyenda??"Ley N° 453: Puedes acceder a la reclamación cuando tus derechos han sido vulnerados.",U=Array.isArray(t.venta_detalles)?t.venta_detalles:Array.isArray(t.details)?t.details:[],T=Math.floor(d),N=Math.round((d-T)*100).toString().padStart(2,"0"),x=`Son ${i.convertToText(T)} ${N}/100 Bolivianos`;let S=null;k&&(S=await fe.toDataURL(`${a.url2}consulta/QR?nit=${a.nit}&cuf=${k}&numero=${c}&t=2`,{errorCorrectionLevel:"M",type:"png",width:110,margin:0,color:{dark:"#000",light:"#FFF"}}));let O=`${this.head()}
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
    <tr><td class="lbl">CÓD. AUTORIZACIÓN</td><td class="val">${k??"—"}</td></tr>
  </table>

  <hr>

  <table class="tbl fs10">
    <tr><td class="lbl">NOMBRE/RAZÓN SOCIAL</td><td class="val">${o(h)}</td></tr>
    <tr><td class="lbl">NIT/CI/CEX</td><td class="val">${o(p)}${o(f?"-"+f:"")}</td></tr>
    <tr><td class="lbl">NRO. CLIENTE</td><td class="val">${o(b)}</td></tr>
    <tr><td class="lbl">FECHA DE EMISIÓN</td><td class="val">${o(u)}</td></tr>
  </table>

  <hr>
  <div class="det-header center">DETALLE</div>`;U.forEach(v=>{const F=v.producto_id??v.product_id??v?.producto?.id??"—",se=o(v.nombre??v.descripcion??v?.producto?.nombre??""),Ee=o(v.unidad??v?.producto?.unidad??""),at=Number(v.cantidad??v.qty??0),je=Number(v.precio??v.precioUnitario??0),ot=Number(v.descuento??v.montoDescuento??0),qe=v.subTotal??at*je-ot;O+=`
      <table class="tbl fs10">
        <tr>
          <td class="left item-desc" colspan="3">${F} - ${se}</td>
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

  <div class="fs10" style="margin-top:6px;">${x}</div>

  <hr>
  <div class="center small">
    ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS,<br>
    EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE DE ACUERDO A LEY
  </div>
  <div class="center small" style="margin-top:4px;">${o(_)}</div>
  <div class="center small" style="margin-top:4px;">“Este documento es la Representación Gráfica de un<br>Documento Fiscal Digital emitido en una modalidad de facturación en línea”</div>
  ${S?`<div class="qr"><img src="${S}" alt="QR"></div>`:""}
</div>`;const C=document.getElementById("myElement");C&&(C.innerHTML=O),new X.Printd().print(C),r(S)}catch(s){n(s)}})}static nota(t,r=!0){return console.log("factura",t),new Promise((n,s)=>{const i=this.numeroALetras(123),a={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}};ie().env,fe.toDataURL(`Fecha: ${t.fecha_emision} Monto: ${parseFloat(t.total).toFixed(2)}`,a).then(l=>{let o="",d="";t.producto&&(o="<tr><td class='titder'>PRODUCTO:</td><td class='contenido'>"+t.producto+"</td></tr>"),t.cantidad&&(d="<tr><td class='titder'>CANTIDAD:</td><td class='contenido'>"+t.cantidad+"</td></tr>");let c=`${this.head()}
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
      <div>Son ${i} ${((parseFloat(t.total)-Math.floor(parseFloat(t.total)))*100).toFixed(2)} /100 Bolivianos</div><hr>
        <!--div style='display: flex;justify-content: center;'>
          <img  src="${l}" style="width: 75px; height: 75px; display: block; margin-left: auto; margin-right: auto;">
        </div--!>
      </div>
      </div>
</body>
</html>`,document.getElementById("myElement").innerHTML=c,r&&new X.Printd().print(document.getElementById("myElement")),n(l)}).catch(l=>{s(l)})})}static cotizacion(t,r,n,s,i=!0){return(s==null||s==="")&&(s=0),new Promise((a,l)=>{const o=me.conversorNumerosALetras,c=new o().convertToText(parseInt(n)),u=y().format("YYYY-MM-DD"),h={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}},f=ie().env;fe.toDataURL(`Fecha: ${u} Monto: ${parseFloat(n).toFixed(2)}`,h).then(p=>{let b=`${this.head()}
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
</table><hr><div class='titulo'>DETALLE</div>`;t.forEach(M=>{b+=`<div style='font-size: 12px'><b> ${M.nombre} </b></div>`,b+=`<div><span style='font-size: 18px;font-weight: bold'>${M.cantidadVenta}</span> ${parseFloat(M.precioVenta).toFixed(2)} 0.00
                    <span style='float:right'>${parseFloat(M.precioVenta*M.cantidadVenta).toFixed(2)}</span></div>`}),b+=`<hr>
<div>${r.comentario===""||r.comentario===null||r.comentario===void 0?"":"Comentario: "+r.comentario}</div>
      <table style='font-size: 8px;'>
      <tr><td class='titder' style='width: 60%'>SUBTOTAL Bs</td><td class='conte2'>${parseFloat(n).toFixed(2)}</td></tr>
      <tr><td class='titder' style='width: 60%'>Descuento Bs</td><td class='conte2'>${parseFloat(s).toFixed(2)}</td></tr>
      <tr><td class='titder' style='width: 60%'>TOTAL Bs</td><td class='conte2'>${parseFloat(n-s).toFixed(2)}</td></tr>
      </table>
      <br>
      <div>Son ${c} ${((parseFloat(n)-Math.floor(parseFloat(n)))*100).toFixed(2)} /100 Bolivianos</div><hr>
      <div style='display: flex;justify-content: center;'>
        <img  src="${p}" style="width: 75px; height: 75px; display: block; margin-left: auto; margin-right: auto;">
      </div></div>
      </div>
</body>
</html>`,document.getElementById("myElement").innerHTML=b,i&&new X.Printd().print(document.getElementById("myElement")),a(p)}).catch(p=>{l(p)})})}static notaCompra(t){return console.log("factura",t),new Promise((r,n)=>{const s=me.conversorNumerosALetras,a=new s().convertToText(parseInt(t.total)),l={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}},o=ie().env;fe.toDataURL(`Fecha: ${t.fecha_emision} Monto: ${parseFloat(t.total).toFixed(2)}`,l).then(async d=>{let c=`${this.head()}
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
</html>`,document.getElementById("myElement").innerHTML=c,new X.Printd().print(document.getElementById("myElement")),r(d)}).catch(d=>{n(d)})})}static reportTotal(t,r){const n=t.filter(a=>a.tipoVenta==="Ingreso").reduce((a,l)=>a+l.montoTotal,0),s=t.filter(a=>a.tipoVenta==="Egreso").reduce((a,l)=>a+l.montoTotal,0),i=n-s;return console.log("montoTotal",i),new Promise((a,l)=>{const o=me.conversorNumerosALetras,d=new o,c=Math.abs(i),u=d.convertToText(parseInt(c)),h={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}},f=ie().env;fe.toDataURL(` Monto: ${parseFloat(i).toFixed(2)}`,h).then(p=>{let b=`${this.head()}
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
</table><hr><div class='titulo'>DETALLE</div>`;t.forEach(g=>{b+=`<div style='font-size: 12px'><b> ${g.user.name} </b></div>`,b+=`<div> ${parseFloat(g.montoTotal).toFixed(2)} ${g.tipoVenta}
          <span style='float:right'> ${g.tipoVenta==="Egreso"?"-":""} ${parseFloat(g.montoTotal).toFixed(2)}</span></div>`}),b+=`<hr>
      <table style='font-size: 8px;'>
      <tr><td class='titder' style='width: 60%'>SUBTOTAL Bs</td><td class='conte2'>${parseFloat(i).toFixed(2)}</td></tr>
      </table>
      <br>
      <div>Son ${u} ${((parseFloat(i)-Math.floor(parseFloat(i)))*100).toFixed(2)} /100 Bolivianos</div><hr>
      <div style='display: flex;justify-content: center;'>
        <img  src="${p}" style="width: 75px; height: 75px; display: block; margin-left: auto; margin-right: auto;">
      </div></div>
      </div>
</body>
</html>`,document.getElementById("myElement").innerHTML=b,new X.Printd().print(document.getElementById("myElement")),a(p)}).catch(p=>{l(p)})})}static reciboCompra(t){return new Promise((r,n)=>{const s=me.conversorNumerosALetras,a=new s().convertToText(parseInt(t.total)),l={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}},o=ie().env;console.log("env",o),fe.toDataURL(`Fecha: ${t.date} Monto: ${parseFloat(t.total).toFixed(2)}`,l).then(d=>{let c=`${this.head()}
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
    </html>`,document.getElementById("myElement").innerHTML=c,new X.Printd().print(document.getElementById("myElement")),r(d)}).catch(d=>{n(d)})})}static reciboPedido(t){return console.log("reciboPedido",t),new Promise((r,n)=>{const s=me.conversorNumerosALetras,a=new s().convertToText(parseInt(t.total)),l={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}},o=ie().env;fe.toDataURL(`Fecha: ${t.date} Monto: ${parseFloat(t.total).toFixed(2)}`,l).then(d=>{let c=`${this.head()}
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
    </html>`,document.getElementById("myElement").innerHTML=c,new X.Printd().print(document.getElementById("myElement")),r(d)}).catch(d=>{n(d)})})}static reciboTranferencia(t,r,n,s){return console.log("producto",t,"de",r,"ha",n,"cantidad",s),new Promise((i,a)=>{const l=me.conversorNumerosALetras,d=new l().convertToText(parseInt(s)),c={errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}},u=ie().env;fe.toDataURL(`de: ${r} A: ${n}`,c).then(h=>{let f=`${this.head()}
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
      <tr><td class='titder' style='width: 60%'>CANTIDAD </td><td class='conte2'>${s+""}</td></tr>
      </table>
      <br>
      <div>Son ${d+""} ${s+""} unidades</div><hr>
      <div style='display: flex;justify-content: center;'>
        <img  src="${h}" style="width: 75px; height: 75px; display: block; margin-left: auto; margin-right: auto;">
      </div></div>
      </div>
    </body>
    </html>`,document.getElementById("myElement").innerHTML=f,new X.Printd().print(document.getElementById("myElement")),i(h)}).catch(h=>{a(h)})})}static head(){return`<html>
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
<div style="width: 300px;">`}static async printFactura(t){const r=me.conversorNumerosALetras,s=new r().convertToText(parseInt(t.total)),i=ie().env,a=await fe.toDataURL(`${i.url2}consulta/QR?nit=${i.nit}&cuf=${t.cuf}&numero=${t.id}&t=2`,{errorCorrectionLevel:"M",type:"png",quality:.95,width:100,margin:1,color:{dark:"#000000",light:"#FFF"}}),l=t.online?"en":"fuera de";let o=`<style>
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
      ${i.razon}<br>Casa Matriz<br>No. Punto de Venta 0<br>
      ${i.direccion}<br>Tel. ${i.telefono}<br>Oruro
    </div>
    <hr>
    <div class='titulo'>NIT</div><div class='titulo2'>${i.nit}</div>
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
    <div>Son ${s} ${((parseFloat(t.total)-Math.floor(t.total))*100).toFixed(0)}/100 Bolivianos</div>
    <hr>
    <div class='titulo2' style='font-size: 9px'>ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS,<br>
    EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE DE ACUERDO A LEY<br><br>
    ${t.leyenda}<br><br>
    “Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido en una modalidad de facturación ${l} línea”</div>
    <div style='display: flex; justify-content: center;'>
      <img src="${a}" />
    </div>
  </div>`;const d=document.getElementById("myElement");d&&(d.innerHTML=o),new X.Printd().print(d)}static async reciboVentaSimple(t,r=!0){try{const n=ie().env||{},s=me.conversorNumerosALetras,i=new s,a=M=>Number(M||0).toFixed(2),l=(M,g="")=>(M??g).toString(),o=Number(t.total??0),d=Math.floor(o),c=Math.round((o-d)*100).toString().padStart(2,"0"),h=`Son ${i.convertToText(d)} ${c}/100 Bolivianos`,f=Array.isArray(t.venta_detalles)?t.venta_detalles:[];let p=`${this.head()}
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
`;f.forEach(M=>{const g=l(M.producto?.nombre??M.nombre??M.descripcion??""),k=Number(M.cantidad??0),E=Number(M.precio??0),_=Number(M.subTotal??k*E),U=l(M.producto_id??M.product_id??M.producto?.id??"");p+=`<div style='font-size: 12px'><b>${U?U+" - ":""}${g}</b></div>`,p+=`
      <div>
        <span style='font-size: 14px;font-weight: bold'>${a(k)}</span>
        <span>${a(E)} 0.00</span>
        <span style='float:right'>${a(_)}</span>
      </div>`}),p+=`
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
</html>`;const b=document.getElementById("myElement");return b&&(b.innerHTML=p),r&&new X.Printd().print(document.getElementById("myElement")),!0}catch(n){throw console.error("reciboVentaSimple error:",n),n}}static ensureMount(){let t=document.getElementById("myElement");return t||(t=document.createElement("div"),t.id="myElement",t.style.display="none",document.body.appendChild(t)),t}static printTicketHtml(t){const r=this.ensureMount();r.innerHTML=t,new X.Printd().print(r)}static fichaDespacho(t){const r=new Date(t.created_at||Date.now()),n=String(r.getDate()).padStart(2,"0"),s=String(r.getMonth()+1).padStart(2,"0"),i=r.getFullYear(),a=`${n}/${s}/${i}`,l=t.detalles||[],o=t.prestamos||[],d=t.user?.name||t.user?.username||"-",c=t.cliente_nombre||t.cliente?.nombre||"-",u=t.observacion||"",h=g=>Number(g||0).toFixed(0),f=l.map(g=>`
      <tr>
        <td class="qty">${Number(g.cantidad||0)}</td>
        <td class="prod">${g.producto_nombre||""}</td>
        <td class="num">${h(g.subtotal)}</td>
      </tr>
    `).join(""),p=o.map(g=>`
      <tr>
        <td class="qty">${Number(g.cantidad||0)}</td>
        <td class="mat">${g.inventario?.nombre||"material"}</td>
        <td class="num">${h(g.efectivo??g.fisico_recibido)}</td>
        <td class="tipo">${String(g.tipo||"-").toUpperCase()}</td>
      </tr>
    `).join(""),b=p?`
        <table class="material-table">
          <thead>
            <tr>
              <th>CANT</th>
              <th>MATERIAL</th>
              <th>MONTO</th>
              <th>TIPO</th>
            </tr>
          </thead>
          <tbody>${p}</tbody>
        </table>`:"",M=`
      <style>
        .despacho-ticket {
          width: 300px;
          font-family: "Times New Roman", serif;
          color: #111;
          font-size: 14px;
          line-height: 1.15;
        }
        .despacho-ticket .top-title {
          display: flex;
          justify-content: flex-end;
          gap: 28px;
          font-size: 16px;
          margin-bottom: 8px;
        }
        .despacho-ticket .row-line {
          font-size: 15px;
        }
        .despacho-ticket .dash {
          border: 0;
          border-top: 1px dashed #222;
          margin: 8px 0;
        }
        .despacho-ticket .tear {
          text-align: center;
          letter-spacing: 2px;
          margin: 12px 0 8px;
          font-weight: bold;
          overflow: hidden;
          white-space: nowrap;
        }
        .despacho-ticket table {
          width: 100%;
          border-collapse: collapse;
        }
        .despacho-ticket th {
          font-size: 18px;
          font-weight: bold;
          text-align: left;
          padding: 2px 0;
        }
        .despacho-ticket td {
          font-size: 18px;
          padding: 2px 0;
          vertical-align: top;
        }
        .despacho-ticket .qty {
          width: 38px;
        }
        .despacho-ticket .prod {
          width: 180px;
          text-align: center;
        }
        .despacho-ticket .num {
          width: 44px;
          text-align: center;
        }
        .despacho-ticket .mat {
          width: 118px;
          text-align: center;
          word-break: break-word;
        }
        .despacho-ticket .tipo {
          width: 84px;
          text-align: right;
        }
        .despacho-ticket .section-title {
          text-align: center;
          font-size: 15px;
          margin-bottom: 4px;
        }
        .despacho-ticket .copy-title {
          text-align: center;
          font-size: 16px;
          margin-bottom: 4px;
        }
        .despacho-ticket .strong {
          font-size: 18px;
          font-weight: bold;
        }
        .despacho-ticket .firma {
          margin-top: 58px;
          text-align: center;
          font-size: 18px;
          font-weight: bold;
        }
        .despacho-ticket .terms {
          margin-top: 10px;
          font-size: 20px;
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
        <div>Observacion: ${u}</div>
        ${b}
        ${p?`
          <div class="tear">########################</div>
          <div class="section-title">Detalle de Prestamo o Venta de material</div>
          <div class="strong">Cliente - ${c}</div>
          ${b}
          <div class="tear">########################</div>
          <div class="copy-title">copia para archivo</div>
          <div class="strong">Cliente - ${c}</div>
          <div class="strong">Fecha: ${a.split("/").reverse().join("-")}</div>
          <div class="strong">Usuario: ${d}</div>
          ${b}
          <div class="firma">FIRMA</div>
          <div class="terms">* Acepto todas las condiciones y terminos de prestamo de envases</div>
        `:""}
      </div>
    `;this.printTicketHtml(M)}static hojaRuta(t){const r=new Date(t.created_at||Date.now()),n=String(r.getDate()).padStart(2,"0"),s=String(r.getMonth()+1).padStart(2,"0"),i=r.getFullYear(),a=t.hoja_hora||`${String(r.getHours()).padStart(2,"0")}:${String(r.getMinutes()).padStart(2,"0")}:${String(r.getSeconds()).padStart(2,"0")}`,l=`${n}/${s}/${i}`,o=t.hoja_fecha_entrega||t.fecha||t.fecha_venta||null,d=o?String(o).slice(0,10).split("-").reverse().join("/"):l,c=t.detalles||[],u=t.prestamos||[],h=c.map(k=>`
      <tr>
        <td>${Number(k.cantidad||0)}</td>
        <td>${k.producto_nombre||""}</td>
      </tr>
    `).join(""),f=u.map(k=>`
      <tr>
        <td>${Number(k.cantidad||0)}</td>
        <td>${k.inventario?.nombre||"material"}</td>
        <td>${k.tipo}</td>
      </tr>
    `).join(""),p=(t.pagos||[]).find(k=>k.estado==="PAGADO")?.metodo||"-",b=t.hoja_cuenta??t.total_pagado??0,M=t.hoja_saldo??t.saldo_pendiente??t.total??0,g=`
      <div style="width:300px;font-family: 'Times New Roman', serif; font-size:14px;">
        <div style="text-align:center;">
          <div>H/R</div>
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
        <div><b>Direccion:</b> ${t.hoja_direccion||t.cliente_direccion||"-"}</div>
        <div><b>Hora:</b> ${a}</div>
        <div><b>Envases:</b> ${t.hoja_envases||"-"}</div>
        <hr>
        <div><b>Observacion:</b> ${t.hoja_observaciones||t.observacion||""}</div>
        <div><b>Total:</b> ${Number(t.total||0).toFixed(2)}</div>
        <div><b>A cuenta:</b> ${Number(b||0).toFixed(2)}</div>
        <div><b>Saldo:</b> ${Number(M||0).toFixed(2)}</div>
        <div><b>Metodo:</b> ${p}</div>
        <div><b>Usuario:</b> ${t.user?.name||t.user?.username||"-"}</div>
      </div>
    `;this.printTicketHtml(g)}static movimientoCaja(t,r="Caja"){const n=new Date(t.created_at||Date.now()),s=String(n.getDate()).padStart(2,"0"),i=String(n.getMonth()+1).padStart(2,"0"),a=n.getFullYear(),l=String(n.getHours()).padStart(2,"0"),o=String(n.getMinutes()).padStart(2,"0"),d=String(n.getSeconds()).padStart(2,"0"),c=`
      <div style="width:300px;font-family: 'Times New Roman', serif; font-size:14px;">
        <div style="text-align:center;border-bottom:1px solid #222;padding-bottom:4px;margin-bottom:8px;">
          <div style="font-size:24px;font-weight:bold;">Comprobante de caja</div>
          <div style="font-size:16px">${s}/${i}/${a} ${l}:${o}:${d}</div>
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
