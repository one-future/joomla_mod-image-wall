jQuery(function($) {
    $("div[class*='imgwall-cell'] svg").each(function() {
        let aspectratio = $(this).width() / $(this).height();

        let heightrestr = $(this).closest("figure").attr("data-hr");

        $(this).css("max-width", heightrestr * aspectratio);

        $(this).next().css("max-width", heightrestr * aspectratio);
    });

    $("[class*='imgwall-wrapper']").each(function() {
        let id = $(this).attr('data-mid');

        $(this).parent().addClass("imgwall-module-" + id);
    });
});