var slideMenu=function(){
	var sp,st,t,m,sa,l,w,gw,ot;
	return{
		build:function(sm,sw,mt,s,sl,h){
			sp=s; st=sw; t=mt;
			m=document.getElementById(sm);
			sa=m.getElementsByTagName('li');
			l=sa.length; w=m.offsetWidth; gw=w/l;
			ot=Math.floor((w-st)/(l-1)); var i=0;
			for(i;i<l;i++){s=sa[i]; s.style.width=gw+'px'; this.timer(s)}
			if(sl!=null){m.timer=setInterval(function(){slideMenu.slide(sa[sl-1])},t)}
		},
		timer:function(s){
			s.onmouseover=function(){clearInterval(m.htimer);clearInterval(m.timer);m.timer=setInterval(function(){slideMenu.slide(s)},t)}
			s.onmouseout=function(){clearInterval(m.timer);clearInterval(m.htimer);m.htimer=setInterval(function(){slideMenu.slide(s,true)},t)}
		},
		slide:function(s,c){
			var cw=parseInt(s.style.width);
			if((cw<st && !c) || (cw>gw && c)){
				var owt=0; var i=0;
				for(i;i<l;i++){
					if(sa[i]!=s){
						var o,ow; var oi=0; o=sa[i]; ow=parseInt(o.style.width);
						if(ow<gw && c){oi=Math.floor((gw-ow)/sp); oi=(oi>0)?oi:1; o.style.width=(ow+oi)+'px';
						}else if(ow>ot && !c){oi=Math.floor((ow-ot)/sp); oi=(oi>0)?oi:1; o.style.width=(ow-oi)+'px'}
						if(c){owt=owt+(ow+oi)}else{owt=owt+(ow-oi)}}}
				s.style.width=(w-owt)+'px';
			}else{clearInterval(m.timer);clearInterval(m.htimer)}
		}
	};
}();