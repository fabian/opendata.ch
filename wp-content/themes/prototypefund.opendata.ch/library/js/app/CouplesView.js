/**
 * Author: CReich
 * Company: Rainbow Unicorn
 * Date: 18.07.2016
 * Created: 15:18
 **/
(function(window){

    CouplesView.prototype.constructor = CouplesView;
    CouplesView.prototype = {
        total_couples: 11,
        speed: 0.35
    };
    
    var ref, controller, $hoverRed, $canvasPush, $hoverBlue, $areaRed, $areaBlue, currentIndex, index, $sideContainer,
        animationTimeLine, isLocked, hammerTime;
    function CouplesView(pController){
        ref = this;
        controller = pController;
    };

    CouplesView.prototype.init = function(){

        currentIndex = ref.getRandomInt(6,10);
        index = 0;
        Logger.log("currentIndex -> " + currentIndex);

        $canvasPush = $('.canvas-push');

        $sideContainer = $('#sb-site');

        for(var a=0;a<=ref.total_couples;++a){
            if(a != currentIndex){
                TweenMax.set($('.couples-'+a+'-0'),{autoAlpha:0});
                TweenMax.set($('.couples-'+a+'-1'),{autoAlpha:0});
            }
        }

        if($canvasPush.length > 0){

            hammerTime = new Hammer($canvasPush[0]);
            hammerTime.on("swipeleft swiperight", function(ev) {

                switch(ev.type)
                {
                    case "swipeleft":
                        ref.animateBlue();
                        break;
                    case "swiperight":
                        ref.animateRed();
                        break;
                }

            });

        }


        $hoverRed = $('.hover.red');
        $hoverBlue = $('.hover.blue');

        $areaRed = $('.colorblock.red');
        $areaBlue = $('.colorblock.blue');

        TweenMax.set($areaRed,{x:'-100%'});
        TweenMax.set($areaBlue,{x:'100%'});

        $hoverRed.mouseover(function(e) {
            ref.animateRed();
        });

        $hoverBlue.mouseover(function(e) {
            ref.animateBlue();
        });

    };

    CouplesView.prototype.getRandomInt = function(min, max){
        return Math.floor(Math.random() * (max - min + 1)) + min;
    };

    CouplesView.prototype.animateBlue = function(){

        if($sideContainer.hasClass('menu-open') || isLocked) return;

        animationTimeLine = new TimelineMax({delay:0, paused:false, onStart: ref.lockAnimation, onComplete: ref.unlockAnimation})
            .to($areaBlue,ref.speed, {x: '0%', ease: Sine.easeOut})
            .to($areaBlue, ref.speed, {delay:0.3, x: '100%', ease: Sine.easeIn})
            .to($areaRed, ref.speed, {x: '0%', ease: Sine.easeOut})
            .to($areaRed, ref.speed, {delay:0.3, x: '-100%', ease: Sine.easeIn})
    };

    CouplesView.prototype.animateRed = function(){

        if($sideContainer.hasClass('menu-open') || isLocked) return;

        animationTimeLine = new TimelineMax({delay:0, paused:false, onStart: ref.lockAnimation, onComplete: ref.unlockAnimation})
            .to($areaRed, ref.speed, {x: '0%', ease: Sine.easeOut})
            .to($areaRed, ref.speed, {delay:0.3, x: '-100%', ease: Sine.easeIn})
            .to($areaBlue, ref.speed, {x: '0%', ease: Sine.easeOut})
            .to($areaBlue, ref.speed, {delay:0.3, x: '100%', ease: Sine.easeIn});
    };

    CouplesView.prototype.lockAnimation = function(){
        isLocked = true;
    };

    CouplesView.prototype.unlockAnimation = function(){
        ref.showNextCouples();
        isLocked = false;
    };

    CouplesView.prototype.showNextCouples = function(){
        if(currentIndex < ref.total_couples){
            currentIndex++;
        } else currentIndex = 0;

        for(var a=0;a<=ref.total_couples;++a){
            if(a != currentIndex){
                TweenMax.set($('.couples-'+a+'-0'),{autoAlpha:0});
                TweenMax.set($('.couples-'+a+'-1'),{autoAlpha:0});
            } else {

                TweenMax.set($('.couples-'+a+'-0'),{x:'-100%',autoAlpha:1});
                TweenMax.set($('.couples-'+a+'-1'),{x:'100%', autoAlpha:1});


                var tl = new TimelineMax({delay:0})
                    .to($('.couples-'+a+'-0'), 0.3, {x:'0%', ease: Power2.easeOut},'go')
                    .to($('.couples-'+a+'-1'), 0.3, {x:'0%', ease: Power2.easeOut},'go')


            }
        }

    };

    window.CouplesView = CouplesView;

}(window));
