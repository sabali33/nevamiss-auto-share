import scheduleForms from "./schedule-forms";
import {Settings} from "./settings";

document.addEventListener(
    'DOMContentLoaded', () => {
    scheduleForms.init();
    Settings.init();
})