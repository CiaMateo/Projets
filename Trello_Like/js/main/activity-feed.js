window.addEventListener("load", function ()
{
    let infScroll = new InfiniteScroll( '#activity-feed',
    {
        path: "/utils/activites/{{#}}",
        append: ".post",
        prefill: true,
        history: false,
    });
});