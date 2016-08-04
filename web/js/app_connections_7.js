var Connection = function() {
    var scope = this;

    $(document).on('click', '.follow_button', {scope: scope}, scope.follow);
    $(document).on('click', '.unfollow_button', {scope: scope}, scope.unfollow);

    $(document).on('click', '.jq-show-invite-form', {scope: scope}, scope.showInviteForm);
    $(document).on('click', '#form-invite-submit', {scope: scope}, scope.submitInviteForm);

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
                if (btn.attr('data-type') === 'small') {
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
                if (btn.attr('data-type') === 'small') {
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

    //
    //SHOW INVITE FORM
    //
    showInviteForm: function(e) {
        e.preventDefault();

        var slug = $(e.target).data('slug');

        var formHolder = $('<div/>', {
            class: 'popup_holder'
        });

        $.ajax({
            type: 'get',
            url: App.router.generate('pokatalk_app_invite'),
            success: function(returned) {
                formHolder.html(returned.html);
                $('body').append(formHolder);
                formHolder.fadeIn('fast');

                //MULTIPLE EMAILS
                var emailInput = $('.jq-multiple-email');
                var emailList = [];
                var emailListInput = $('.jq-invite-list');
                emailInput.tagsinput({
                    tagClass: ''
                });

                //check if it'e an email
                emailInput.on('itemAdded', function(event) {
                    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
                    //if is valid - add to list
                    if(pattern.test(event.item)) {
                        emailList.push(event.item);
                        var emailListJson = JSON.stringify(emailList);
                        emailListInput.val(emailListJson);
                    }
                    //if is invalid - don't add to list and make it red
                    else {
                        var tag = $('.bootstrap-tagsinput .tag').last();
                        tag.addClass('invalid');
                    }
                });

                //if item is removed - remove it from list
                emailInput.on('itemRemoved', function(event) {
                    var index = emailList.indexOf(event.item);
                    emailList.splice(index, 1);
                    var emailListJson = JSON.stringify(emailList);
                    emailListInput.val(emailListJson);
                });

                var closeLink = formHolder.find('.jq-close-popup');
                closeLink.on('click', function(e) {
                    e.preventDefault();
                    formHolder.fadeOut('fast', function() {
                        formHolder.remove();
                    })
                })
            }
        });
    },

    submitInviteForm: function (e) {
        e.preventDefault();

        var emails = $('.jq-invite-list');
        var emailsVal = emails.val();

        if(!emailsVal) {
            emails.parent('.form-group').addClass('error_field');
            emails.prev('.error_message').removeClass('hidden');

            return;
        }

        $.ajax({
            type: 'post',
            url : App.router.generate('pokatalk_app_invite_send'),
            data: {
                message: $('#form-invite-message').val(),
                emails: JSON.parse(emailsVal)
            },
            success: function() {
                $(".invite-form-holder").hide();
                $(".popup_holder").hide();
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