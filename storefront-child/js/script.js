jQuery(document).ready(function($) {
    $('#city-search').on('input', function() {
        var searchQuery = $(this).val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'city_search',
                search_query: searchQuery
            },
            success: function(response) {
                var tableBody = '';
                $.each(response, function(index, city) {
                    tableBody += '<tr><td>' + city.post_title + '</td><td>' + city.country + '</td><td>' + city.latitude + '</td><td>' + city.longitude + '</td></tr>';
                });
                $('#cities-table tbody').html(tableBody);
            }
        });
    });
});
