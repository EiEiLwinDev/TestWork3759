
jQuery(document).ready(function($) {
    $('#city-search').on('keyup', function() {
        var searchQuery = $(this).val();
        $.ajax({
            url: customDataTable.ajax_url,
            type: 'GET',
            data: {
                action: 'search_city',
                query: searchQuery
            },
            success: function(response) {
                $('#custom-data-table tbody').html(response);
            }
        });
    });
}); 
