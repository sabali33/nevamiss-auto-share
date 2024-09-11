import $ from 'jquery';
import Request from "./request";

export const sortElements = (selector:string) => {
    $(selector).sortable({
        placeholder: 'sortable-placeholder',
        update: function(event, ui) {
            const sortedIDs = $(this).sortable('toArray', {
                attribute: 'data-schedule-post-id'
            });
            const scheduleElement = $(ui.item).closest('.schedule-overview-wrap');
            const scheduleId = scheduleElement.data('schedule-id');

            const {ajax_url: ajaxUrl, nonce, messages} = window.nevamiss;

            scheduleElement.prepend(`<span class="pending">${messages.sort_pending_text}</span>`);

            Request.post(ajaxUrl, {action: 'nevamiss_sort_queue_posts', data: sortedIDs, scheduleId, nonce}).then( (data) => {
                scheduleElement.find('span').first().attr('class', 'message notice-success').text(messages.sort_success_text);
            }).catch((err) => {
                scheduleElement.find('span').first().attr('class', 'message message-error').text( messages.sort_failure_text );
            });
            emptyElementAfter(scheduleElement.find('span').first());
        },
    });
}

const emptyElementAfter = (element:HTMLElement, duration: number = 5000) => {

    setTimeout(()=> {
        element.remove()
    }, duration)
}