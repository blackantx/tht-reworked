var timerlen = 5;
var slideAniLen = 250;

var timerID = new Array();
var startTime = new Array();
var obj = new Array();
var endHeight = new Array();
var moving = new Array();
var dir = new Array();

function ajaxSlide(id, url, callback) {
    if(callback == undefined || callback == null) {
        callback = function() {};
    }
    $.get(url, function(data) {
        if(document.getElementById(id).style.display == 'none') {
            document.getElementById(id).innerHTML = data;
            $("#"+id).slideDown(500, callback);
        }
        else {
            $("#"+id).slideUp(500, function() {
                document.getElementById(id).innerHTML = data;
                $("#"+id).slideDown(500, callback);
            });
        }

    });
}

function ajax(id, url, callback) {
    if(callback == undefined || callback == null) {
        callback = function() {};
    }
    $.get(url, function(data) {
        document.getElementById(id).innerHTML = data;
        callback();
    });
}

function tblshowhide(id, imgid, url) {
    if(document.getElementById(id).style.display == "none") {
        $("#"+id).slideDown(500, function() {
            document.getElementById(imgid).src = url+"themes/icons/bullet_toggle_minus.png";
                                        });
    }
    else {
        $("#"+id).slideUp(500, function() {
            document.getElementById(imgid).src = url+"themes/icons/bullet_toggle_plus.png";
                                        });
    }
}

function slidedown(objname){
        if(moving[objname])
                return;
        if(document.getElementById(objname).style.display != "none")
                return; // cannot slide down something that is already visible

        moving[objname] = true;
        dir[objname] = "down";
        startslide(objname);
}

function slideup(objname){
        if(moving[objname])
                return;

        if(document.getElementById(objname).style.display == "none")
                return; // cannot slide up something that is already hidden

        moving[objname] = true;
        dir[objname] = "up";
        startslide(objname);
}

function startslide(objname){
        obj[objname] = document.getElementById(objname);

        endHeight[objname] = parseInt(obj[objname].style.height);
        startTime[objname] = (new Date()).getTime();

        if(dir[objname] == "down"){
                obj[objname].style.height = "1px";
        }

        obj[objname].style.display = "block";

        timerID[objname] = setInterval('slidetick(\'' + objname + '\');',timerlen);
}

function slidetick(objname){
        var elapsed = (new Date()).getTime() - startTime[objname];

        if (elapsed > slideAniLen)
                endSlide(objname)
        else {
                var d =Math.round(elapsed / slideAniLen * endHeight[objname]);
                if(dir[objname] == "up")
                        d = endHeight[objname] - d;

                obj[objname].style.height = d + "px";
        }

        return;
}

function endSlide(objname){
        clearInterval(timerID[objname]);

        if(dir[objname] == "up")
                obj[objname].style.display = "none";

        obj[objname].style.height = endHeight[objname] + "px";

        delete(moving[objname]);
        delete(timerID[objname]);
        delete(startTime[objname]);
        delete(endHeight[objname]);
        delete(obj[objname]);
        delete(dir[objname]);

        return;
}

//fgnass.github.com/spin.js
(function(C,v,w){function D(a,c){for(var d=~~((a[h]-1)/2),b=1;b<=d;b++)c(a[b*2-1],a[b*2])}function k(a){var c=v.createElement(a||"div");D(arguments,function(a,b){c[a]=b});return c}function l(a,c,d){d&&!d[E]&&l(a,d);a.insertBefore(c,d||null);return a}function N(a,c){var d=[f,c,~~(a*100)].join("-"),b="{"+f+":"+a+"}",e;if(!I[d]){for(e=0;e<n[h];e++)try{F.insertRule("@"+(n[e]&&"-"+n[e].toLowerCase()+"-"||"")+"keyframes "+d+"{0%{"+f+":1}"+c+"%"+b+"to"+b+"}",F.cssRules[h])}catch(g){}I[d]=1}return d}function G(a,
c){var d=a[o],b,e;if(d[c]!==w)return c;c=c.charAt(0).toUpperCase()+c.slice(1);for(e=0;e<n[h];e++)if(b=n[e]+c,d[b]!==w)return b}function m(a){D(arguments,function(c,d){a[o][G(a,c)||c]=d});return a}function O(a){D(arguments,function(c,d){a[c]===w&&(a[c]=d)});return a}var j="width",h="length",s="radius",g="lines",x="color",f="opacity",o="style",H="height",y="left",z="top",p="px",E="parentNode",A="position",J="absolute",t="transform",K="Origin",L="coord",u=o+"Sheets",n="webkit0Moz0ms0O".split(0),I={},
M;l(v.getElementsByTagName("head")[0],k(o));var F=v[u][v[u][h]-1],u=function(a){this.opts=O(a||{},g,12,"trail",100,h,7,j,5,s,10,x,"#000",f,0.25,"speed",1)},B=u.prototype={spin:function(a){var c=this,d=c.el=c[g](c.opts);a&&l(a,m(d,y,~~(a.offsetWidth/2)+p,z,~~(a.offsetHeight/2)+p),a.firstChild);if(!M){var b=c.opts,e=0,i=20/b.speed,j=(1-b[f])/(i*b.trail/100),h=i/b[g];(function P(){e++;for(var a=b[g];a;a--){var k=Math.max(1-(e+a*h)%i*j,b[f]);c[f](d,b[g]-a,k,b)}c.Timeout=c.el&&C.setTimeout(P,50)})()}return c},
stop:function(){var a=this.el;C.clearTimeout(this.Timeout);a&&a[E]&&a[E].removeChild(a);this.el=w;return this}};B[g]=function(a){function c(c,b){return m(k(),A,J,j,a[h]+a[j]+p,H,a[j]+p,"background",c,"boxShadow",b,t+K,y,t,"rotate("+~~(360/a[g]*e)+"deg) translate("+a[s]+p+",0)","borderRadius","100em")}for(var d=m(k(),A,"relative"),b=N(a[f],a.trail),e=0,i;e<a[g];e++)i=m(k(),A,J,z,1+~(a[j]/2)+p,t,"translate3d(0,0,0)","animation",b+" "+1/a.speed+"s linear infinite "+(1/a[g]/a.speed*e-1/a.speed)+"s"),
a.shadow&&l(i,m(c("#000","0 0 4px #000"),z,2+p)),l(d,l(i,c(a[x],"0 0 1px rgba(0,0,0,.1)")));return d};B[f]=function(a,c,d){a.childNodes[c][o][f]=d};var r="group0roundrect0fill0stroke".split(0);(function(){var a=m(k(r[0]),"behavior","url(#default#VML)");if(!G(a,t)&&a.adj){for(a=0;a<r[h];a++)F.addRule(r[a],"behavior:url(#default#VML)");B[g]=function(){function a(){return m(k(r[0],L+"size",i+" "+i,L+K,-e+" "+-e),j,i,H,i)}function d(d,h,i){l(n,l(m(a(),"rotation",360/b[g]*d+"deg",y,~~h),l(m(k(r[1],"arcsize",
1),j,e,H,b[j],y,b[s],z,-b[j]/2,"filter",i),k(r[2],x,b[x],f,b[f]),k(r[3],f,0))))}var b=this.opts,e=b[h]+b[j],i=2*e,n=a(),o=~(b[h]+b[s]+b[j])+p,q;if(b.shadow)for(q=1;q<=b[g];q++)d(q,-2,"progid:DXImage"+t+".Microsoft.Blur(pixel"+s+"=2,makeshadow=1,shadow"+f+"=.3)");for(q=1;q<=b[g];q++)d(q);return l(m(k(),"margin",o+" 0 0 "+o,A,"relative"),n)};B[f]=function(a,d,b,e){e=e.shadow&&e[g]||0;a.firstChild.childNodes[d+e].firstChild.firstChild[f]=b}}else M=G(a,"animation")})();C.Spinner=u})(window,document);

// jQuery Plugin
$.fn.spin = function(opts) {
  this.each(function() {
    var $this = $(this),
        spinner = $this.data('spinner');

    if (spinner) {
        spinner.stop();
        if (opts === false)
            $this.removeData('spinner');
    }
    if (opts !== false) {
      opts = $.extend({color: $this.css('color')}, opts);
      spinner = new Spinner(opts).spin(this);
      $this.data('spinner', spinner);
    }
  });
  return this;
};
