var Connection = function() {
    var scope = this;

    $(document).on('click', '.follow_button', {scope: scope}, scope.follow);
    $(document).on('click', '.unfollow_button', {scope: scope}, scope.unfollow);

};

Connection.prototype = {
    follow: function(e) {
        var btn = $(this);
        var btnHolder = btn.parent('.follow_button_holder');
        App.disableButton(btn);

        $.ajax({
            type : 'post',
            url : btn.attr('data-follow-url'),
            data : {
                type: btn.attr('data-type')
            },
            success: function(returned) {
                btnHolder.html(returned.response);

                //update counter
                if (btnHolder.hasClass('in_cover_content')) {
                    var followersCount = $('.user-followers-count');
                    var followersText = $('.users-follower-text');
                    App.increaseCounter(followersCount, followersText, 'Follower');
                }
                else {
                    var connectionsFollowerCount = btnHolder.parent().find('.connection-list-followers-count');
                    var connectionsFollowerCountText = btnHolder.parent().find('.connection-list-followers-text');
                    App.increaseCounter(connectionsFollowerCount, connectionsFollowerCountText, 'follower');

                    var userFollowingCount = $('.user-following-count');
                    App.increaseCounter(userFollowingCount);
                }

                //if small button - update large button
                if (btn.attr('data-type') === 'large') {
                    var connectionPanel = $('.user_connection_panel');
                    if (connectionPanel.length) {
                        var bigButton = connectionPanel.find('button[data-type="large"]');
                        bigButton.removeClass('follow_button btn-primary-white-hover').addClass('unfollow_button btn-gray-outline').empty();
                    }
                }
            }
        });

    },

    unfollow: function(e) {
        var btn = $(this);
        var btnHolder = btn.parent('.follow_button_holder');
        App.disableButton(btn);

        $.ajax({
            type : 'post',
            url : btn.attr('data-unfollow-url'),
            data : {
                user_id : btn.attr('data-user-id'),
                type: btn.attr('data-type')
            },
            success: function(returned) {
                btnHolder.html(returned.response);

                //update counter
                if(btnHolder.hasClass('in_cover_content')) {
                    var followersCount = $('.user-followers-count');
                    var followersText = $('.users-follower-text');
                    App.decreaseCounter(followersCount);
                }
                else {
                    var connectionsFollowerCount = btnHolder.parent().find('.connection-list-followers-count');
                    var connectionsFollowerCountText = btnHolder.parent().find('.connection-list-followers-text');
                    App.decreaseCounter(connectionsFollowerCount, connectionsFollowerCountText, 'follower');

                    var userFollowingCount = $('.user-following-count');
                    App.decreaseCounter(userFollowingCount);
                }

                //if small button - update large button
                if (btn.attr('data-type') === 'large') {
                    var connectionPanel = $('.user_connection_panel');
                    if (connectionPanel.length) {
                        var bigButton = connectionPanel.find('button[data-type="large"]');
                        bigButton.removeClass('unfollow_button btn-gray-outline').addClass('follow_button btn-primary-white-hover');
                        bigButton.html('<i class="icon-member_add"></i> Follow');
                    }
                }
            }
        });
    },

    init: function() {
        new Connection();
    }
};

(function() {
    var conn = new Connection();
})();