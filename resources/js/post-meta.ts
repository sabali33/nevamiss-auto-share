
const jQuery = window.jQuery;
export class PostMeta {
    public static init(){
        const accountList = document.querySelector('.nevamiss-instant-share-list') as HTMLUListElement;
        if(!accountList){
            return;
        }
        accountList.addEventListener('click', (event) => {
            event.preventDefault();
            const target = event.target as HTMLElement;

            if(!target || target.tagName.toLowerCase() !== 'a'){
                return;
            }
            target.insertAdjacentHTML('afterend', '<span class="spinner is-active"></span>');
            const url = target.getAttribute('href');
            jQuery.get(url).done((res) => {
                console.log(res)
                accountList.querySelector('.spinner').remove();
                target.insertAdjacentHTML('afterend', '<span class="success-message">Successfully Shared </span>')

            }).fail(err =>{
                console.log(err)
                accountList.querySelector('.spinner').remove();
                target.insertAdjacentHTML('afterend', '<span class="error-message"> Error Sharing </span>')

            });
        })
    }
}