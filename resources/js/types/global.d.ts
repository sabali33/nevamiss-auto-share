declare global {
    interface Window {
        nevamiss: {
            ajax_url: any;
            nonce: string;
            messages: Array<string, string>
        };
        jQuery: any; // or you can specify a proper type for jQuery if needed
    }
}
export {};