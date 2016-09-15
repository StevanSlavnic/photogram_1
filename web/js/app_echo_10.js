var App = {
    router: Routing,

    //
    //FLESH MESSAGES
    //
    fleshMessage: function() {
        var message = $('.jq-flash-message');

        if(message.length) {
            message.slideDown('fast').delay(3000);
            message.slideUp('fast');
        }
    },

    //
    //ENABLE AND DISABLE BUTTONS
    //
    disableButton: function(button, text, callback) {
        button.addClass('disabled');
        button.prop('disabled', true);

        if(typeof text !== 'undefined') {
            button.text(text);
        }
        if(typeof callback !== 'undefined') {
            callback(button, text);
        }
    },

    enableButton: function(button, text, callback) {
        button.removeClass('disabled');
        button.prop('disabled', false);
        button.removeAttr('disabled');
        if(typeof text !== 'undefined') {
            button.text(text);
        }
        if(typeof callback !== 'undefined') {
            callback(button, text);
        }
    },

    //
    //START AND STOP SPINNER
    //
    startSpinner: function(spinnerHolder) {
        spinnerHolder.addClass('spinner-holder-active');

        var spinner = $('<i/>', {
            class : 'icon-spinner'
        }).appendTo(spinnerHolder);
    },

    stopSpinner: function(spinnerHolder) {
        spinnerHolder.removeClass('spinner-holder-active');
        var spinner =$('.icon-spinner');
        spinner.remove();
    },

    //
    //INCREASE AND DECREASE COUNTER
    //
    increaseCounter: function(counter, textHolder, text) {
        counter.each(function() {
            var currnet = $(this);
            var countInt = parseInt(currnet.text());

            ++countInt;
            currnet.html(countInt);

            if(typeof textHolder !== 'undefined') {
                if (countInt == 1) {
                    textHolder.text(text);
                }
                else {
                    textHolder.text(text + 's');
                }
            }
        });
    },

    decreaseCounter: function(counter, textHolder, text) {
        counter.each(function() {
            var currnet = $(this);
            var countInt = parseInt(currnet.text());

            --countInt;
            currnet.html(countInt);

            if(typeof textHolder !== 'undefined') {
                if (countInt == 1) {
                    textHolder.text(text);
                }
                else {
                    textHolder.text(text + 's');
                }
            }
        });
    },

    //
    //SCROLL TO
    //
    scrollTo: function() {
        if(window.location.hash) {
            var elem = $( window.location.hash);
            $('html, body').animate({
                scrollTop: elem.offset().top
            }, 1000);
        }
    },

    //
    //REDIRECT
    //
    redirectToRoute: function(route, args) {
        this.redirect(this.router.generate(route, args));
    },

    redirect: function(url) {
        top.location.href = url;
    },

    //
    //LAZY LOADING
    //
    lazyLoadingUserConnetions: function() {
        var profileFollowers = $('#profile_followers');
        var profileFollowing = $('#profile_following');

        this.initLazyLoad(profileFollowers, '.itm');
        this.initLazyLoad(profileFollowing, '.itm');
    },

    lazyLoadingPosts : function() {
        var postsHolder = $('.posts-holder');
        this.initLazyLoad(postsHolder, '.itm');
    },

    initLazyLoad : function(hodlerElem, itemSelector) {
        if(hodlerElem.length) {

            hodlerElem.infinitescroll('destroy');
            hodlerElem.data('infinitescroll', null);

            hodlerElem.infinitescroll({
                debug        : true,
                navSelector  : ".pagination",
                nextSelector : ".pagination span.next a",
                itemSelector : itemSelector,
                loadingText  : "Loading",
                loading: {
                    finished: undefined,
                    finishedMsg: "<div class='col-lg-12'>That's all, folks!</div>",
                    img: 'http://www.ocli.lk/wp-content/uploads/2015/07/133297-blankpic1.png', //hack for removing loading image - transparent png
                    msg: null,
                    msgText: "<em>Loading...</em>",
                    selector: null,
                    speed: 'fast',
                    start: undefined
                }
            });
        }
    },

    initLazyLoadJSON : function(hodlerElem, itemSelector) {
        if(hodlerElem.length) {

            hodlerElem.infinitescroll('destroy');
            hodlerElem.data('infinitescroll', null);

            hodlerElem.infinitescroll({
                debug        : true,
                navSelector  : ".pagination",
                nextSelector : ".pagination span.next a",
                itemSelector : itemSelector,
                loadingText  : "Loading",
                dataType: 'json',
                appendCallback: false,
                loading: {
                    finished: undefined,
                    finishedMsg: "<div class='col-lg-12'>That's all, folks!</div>",
                    img: 'http://www.ocli.lk/wp-content/uploads/2015/07/133297-blankpic1.png',
                    msg: null,
                    msgText: "<em>Loading...</em>",
                    selector: null,
                    speed: 'fast',
                    start: undefined
                }
            },function(json, opts) {

                console.log(json);
                var page = opts.state.currPage;
                $('.pagination_holder_posts').remove();
                hodlerElem.append(json.html);

                // Do something with JSON data, create DOM elements, etc ..
            });
        }
    },

    //
    //COMMUNITY
    //
    communityFilter : function(){
        $(document).on('click', '.community_nav > a', function(){
            var currnetLink = $(this);
            var otherLink = currnetLink.siblings('a');
            var myNetwork = currnetLink.attr('data-my-network');

            $.get(App.router.generate('pokatalk_app_community_posts') + '?myNetwork='+myNetwork, function (data) {
                var postsHolder = $('.posts-holder');

                $('.pagination_holder_posts').remove();

                postsHolder.html(data.html);

                //set css class for active tab
                otherLink.removeClass('community_filter_active');
                currnetLink.addClass('community_filter_active');

                App.initLazyLoadJSON(postsHolder, '.itm');
            })
        });
    },

    positionCommunityUserHolder: function() {
        var userHolder = $('.user_holder.small');

        if(userHolder.length) {
            $(window).on('scroll', function() {
                if($(window).scrollTop() > 20) {
                    userHolder.stop().animate({
                        top: 20
                    }, 400);
                }
                else {
                    userHolder.stop().animate({
                        top: 86
                    }, 400);
                }
            });
        }
    },

    //
    //INIT
    //
    init: function() {
        this.communityFilter();
        this.fleshMessage();
        this.lazyLoadingUserConnetions();
        this.lazyLoadingPosts();
        this.scrollTo();
        this.positionCommunityUserHolder();
    }
};

(function() {
    App.init();
})();