(function ($) {
    $(document).ready(function () {
        const originalMenuQuickSearch = wpNavMenu.menuQuickSearch; // Save the original function

        wpNavMenu.menuQuickSearch = function(searchType, req) { // Override the function to out handler
            console.log(req);
            const searchTerm = req.term.trim();

            if (searchTerm.startsWith('slug:')) { // Check if the search term starts with "slug:"
                const slug = searchTerm.replace('slug:', '').trim(); // Get the slug
                req.term = ''; // Clear the search term

                $.post(SearchSlug.ajaxurl, {
                    action: 'search_slug_nav_menu',
                    slug: slug,
                    post_type: searchType,
                    nonce: SearchSlug.nonce,
                }).done(function (response) {
                    req.response(response);
                });
            } else {
                originalMenuQuickSearch.call(this, searchType, req);
            }
        };
    });
})(jQuery);