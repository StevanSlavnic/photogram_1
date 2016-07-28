/**
 * Created by stevan on 7/17/16.
 */
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
            type : 'POST',
            url : btn.attr('data-follow-url'),
            data : {
                type: btn.attr('data-type')
            },
            success: function(returned) {
                btnHolder.html(returned.response);
            }
        });

    },

    unfollow: function(e) {
        var btn = $(this);
        var btnHolder = btn.parent('.follow_button_holder');
        App.disableButton(btn);

        $.ajax({
            type : 'POST',
            url : btn.attr('data-unfollow-url'),
            data : {
                user_id : btn.attr('data-user-id'),
                type: btn.attr('data-type')
            },
            success: function(returned) {
                btnHolder.html(returned.response);

//                3

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