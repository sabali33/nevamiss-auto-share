declare global {
    interface Window {
        nevamiss: {
            ajax_url: any;
        };
        jQuery: any; // or you can specify a proper type for jQuery if needed
    }
}
export {};