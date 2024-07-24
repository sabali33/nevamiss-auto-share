
export class Settings {
    static init(){
        document.querySelectorAll('[name="networks_to_post[]"]').forEach(element => {
            element.addEventListener('change', (e)=> {
                const target = e.target as HTMLInputElement;
                const subFieldElement = document.querySelector(`.sub-field-wrap.${target.value}`)

                this.toggleSubFields(subFieldElement, target.checked)

                console.log(target.checked);
            })
        })
    }

    private static toggleSubFields(subFieldElement: Element, checked: boolean) {
        const inputsType = 'input[type],textarea,select'
        const allInputsElements = subFieldElement.querySelectorAll(inputsType);
        if(checked){
            subFieldElement.classList.add('active');
            allInputsElements.forEach( inputElement =>{
                if(inputElement.hasAttribute('disabled')){
                    inputElement.removeAttribute('disabled')
                }
            })
            return;
        }
        subFieldElement.classList.remove('active');
        allInputsElements.forEach(inputElement => {
            inputElement.setAttribute('disabled', 'true')
        })

    }
}