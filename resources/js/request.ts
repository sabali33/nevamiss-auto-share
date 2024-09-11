import jQuery from 'jquery';

type RequestClientType = {
    get:(url:string, options: object) => void,
    post:(url:string, options: object) => void,
}
class Request {
    constructor(private requestClient: RequestClientType) {
    }
    public async  get (url:string, options: object) {
        return this.requestClient.get(url, options)
    }
    public async  post (url:string, options: object) {
        return this.requestClient.post(url, options)
    }
}
export default new Request(jQuery)