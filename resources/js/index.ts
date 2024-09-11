import scheduleForms from "./schedule-forms";
import {Settings} from "./settings";
import {PostMeta} from "./post-meta";
import {sortElements} from "./sort";

document.addEventListener(
    'DOMContentLoaded', () => {
    scheduleForms.init();
    Settings.init();
    PostMeta.init();
    sortElements('.wp-posts-list');
})
