import $ from 'jquery';
import Request from "./request";

export const sortElements = (selector:string) => {
    $(selector).sortable({
        placeholder: 'sortable-placeholder',
        update: function(event, ui) {
            const sortedIDs = $(this).sortable('toArray', {
                attribute: 'data-schedule-post-id'
            });
            const scheduleId = $(ui.item).closest('.schedule-overview-wrap').data('schedule-id');
            const {ajax_url: ajaxUrl, nonce} = window.nevamiss;
            Request.post(ajaxUrl, {action: 'nevamiss_sort_queue_posts', data: sortedIDs, scheduleId, nonce}).then( data => {
                console.log(data)
            }).catch(err => {
                console.log(err)
            })
        },
    });
}