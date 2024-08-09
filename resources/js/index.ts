import scheduleForms from "./schedule-forms";
import {Settings} from "./settings";
import {PostMeta} from "./post-meta";

document.addEventListener(
    'DOMContentLoaded', () => {
    scheduleForms.init();
    Settings.init();
    PostMeta.init()
})
