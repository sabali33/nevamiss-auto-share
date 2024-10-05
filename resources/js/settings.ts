
enum XAPIVersionType {
    V1 = 'v1',
    V2 = 'v2'
}
export class Settings {
    private static currentXVersionSelected?: XAPIVersionType;
    static init(){

        document.querySelectorAll<HTMLInputElement>('[name="networks_to_post[]"], [name="x[version]"], .parent-field').forEach((element: HTMLInputElement) => {

            const subFieldElement = document.querySelector(`.sub-field-wrapper.${element.value}`);

            if(!subFieldElement){
                return;
            }
            this.toggleSubFields(subFieldElement, element.checked);

            element.addEventListener('change', (e)=> {
                const target = e.target as HTMLInputElement;
                const subFieldElement = document.querySelector(`.sub-field-wrapper.${target.value}`);

                if(!subFieldElement){
                    return;
                }
                this.toggleSubFields(subFieldElement, target.checked);
            })
        });

        document.querySelectorAll<HTMLInputElement>('[name="x[version]"]').forEach((element:HTMLInputElement) => {
            const subFieldElement = document.querySelector(`.sub-field-wrapper.${element.value}`);

            if(!subFieldElement){
                return;
            }

            this.toggleSubFields(subFieldElement, element.checked);

            if(element.checked){
                Settings.currentXVersionSelected = element.value as XAPIVersionType;
            }

            element.addEventListener('change', (e)=> {
                const target = e.target as HTMLInputElement;
                const subFieldElement = document.querySelector(`.sub-field-wrapper.${target.value}`);
                const subFieldElementToHide = document.querySelector(`.sub-field-wrapper.${Settings.currentXVersionSelected}`);

                if(!subFieldElement  || !subFieldElementToHide){
                    return;
                }

                this.toggleSubFields(subFieldElement, target.checked);
                this.toggleSubFields(subFieldElementToHide, false);
                Settings.currentXVersionSelected = target.value as XAPIVersionType;
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

