
// Load date time picker on doc ready
$('#datetimepicker').datetimepicker({
    format: 'm/d/Y H:i',
    step: 15
});

// Get the container object
$container = $("body");

// Set the loading on contrainer when ajax starts/stops
$(document).on({
    ajaxStart: function() {
        $container.addClass("loading");
    },
    ajaxStop: function() {
        $container.removeClass("loading");
    }
});

// Report form submission
$('.report-form').on('submit', function () {
    var $this = $(this),
        $datetime = $this.find('#datetimepicker'),
        $contentContainer = $('.report-container');

    // Make sure the datetime has a value
    if ($datetime.val().length > 0) {

        // Make the content container hidden
        $contentContainer.addClass('hidden');

        // Send the ajax call
        $.ajax({
            type: 'POST',
            url: "/get-report",
            dataType: 'html',
            data: {
                datetime: $datetime.val()
            }
        }).done(function (data) {
            // Remove the hidden class now
            $contentContainer.removeClass('hidden');
            $contentContainer.html(data);
        });
    }

    // Don't trigger default event
    return false;
});
