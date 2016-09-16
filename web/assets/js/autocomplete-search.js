/**
 * Created by stevan on 8/13/16.
 */
$(document).ready(function(){
    $("#message_recipient").on('keyup', function() { // everytime keyup event
        var input = $(this).val(); // We take the input value
        if ( input.length >= 2 ) { // Minimum characters = 2 (you can change)
            $('#match').html('<img src="' + window.loader + '" />'); // Loader icon apprears in the <div id="match"></div>
            var data = {input: input}; // We pass input argument in Ajax
            $.ajax({
                type: "POST",
                url: ROOT_URL + "user/inbox/new-message/ajax/autocomplete/update/data", // call the php file ajax/tuto-autocomplete.php (check the routine we defined)
                data: data, // Send dataFields var
                dataType: 'json', // json method
                timeout: 3000,
                success: function(response){ // If success
                    $('#match').html(response.usernameList); // Return data (UL list) and insert it in the <div id="match"></div>
                    $('#matchList li').on('click', function() { // When click on an element in the list
                        $('#message_recipient').val($(this).text()); // Update the field with the new element
                        $('#match').text(''); // Clear the <div id="match"></div>
                    });
                },
                error: function() { // if error
                    $('#match').text('Problem!');
                }
            });
        } else {
            $('#match').text(''); // If less than 2 characters, clear the <div id="match"></div>
        }
    });
});