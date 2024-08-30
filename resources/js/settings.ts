
export class Settings {
    static init(){
        document.querySelectorAll('[name="networks_to_post[]"], .parent-field').forEach((element: HTMLInputElement) => {

            const subFieldElement = document.querySelector(`.sub-field-wrapper.${element.value}`)
            this.toggleSubFields(subFieldElement, element.checked);

            element.addEventListener('change', (e)=> {
                const target = e.target as HTMLInputElement;
                const subFieldElement = document.querySelector(`.sub-field-wrapper.${target.value}`)

                this.toggleSubFields(subFieldElement, target.checked);
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

