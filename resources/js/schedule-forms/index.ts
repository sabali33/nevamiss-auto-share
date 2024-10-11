import flatpickr from "flatpickr";

class ScheduleForms<O extends typeof flatpickr> {
    private readonly datePicker: O;
    private negatedWrappers: Element[];
        public constructor(datepicker:O) {
            this.datePicker = datepicker;
        }

        public init() {
            this.hideUnselectedOptions();
            this.negatedWrappers =  Array.from(document.querySelectorAll("[data-repeat-frequency^='!']"));
            this.attachDateToField();

            const repeatFrequencyField = document.querySelector('#repeat-frequency');

            if(!repeatFrequencyField){
                return;
            }
            repeatFrequencyField.addEventListener(
                'change',
                (event)=>{
                    const target = event.target as HTMLSelectElement;

                    const value = target.value;

                    this.toggleWrappers(
                        document.querySelectorAll(`.sub-field-wrapper.active`),
                        document.querySelectorAll(`[data-repeat-frequency=${value}]`)
                    );
                    this.maybeToggleNegatedWrappers(this.negatedWrappers, value);

                })
            document.querySelector('.schedule-form')?.addEventListener(
                'click',
                (event) =>{

                    const target = event.target as HTMLButtonElement;

                    if(target.tagName.toLowerCase() !== 'button'){
                        return;
                    }

                    event.preventDefault();

                    const wrapper = target.closest('.sub-field-wrapper');

                    if(target.classList.contains('remove')){
                        wrapper?.remove();
                        return;
                    }

                    const cloneWrapper = wrapper?.cloneNode(true) as HTMLElement;

                    const updatedCloneWrapper = this.updateElementsIds(cloneWrapper);

                    wrapper?.insertAdjacentHTML('beforeend', '<button class="remove"> X </button>');
                    wrapper?.insertAdjacentElement('afterend', updatedCloneWrapper);

                    wrapper?.querySelector('button').remove();
                    this.attachDateToField()
            })

        }

    private hideUnselectedOptions() {

        const wrappers = Array.from(document.querySelectorAll<HTMLElement>('.sub-field-wrapper'));
        if(wrappers.length < 1){
            return;
        }
        this.loop(wrappers, (wrapper, field) => {
            if(wrapper.classList.contains('active')){
                return;
            }

            this.disableField(field)
        })
    }
    private disableField(field:Element){
        field.setAttribute('disabled', 'disabled')
    }
    private enableWrapper(wrapper:Element){
        wrapper.querySelectorAll('select, input').forEach( field => {
            field.removeAttribute('disabled')
        })
    }
    private disableWrapper(wrapper:Element){
        wrapper.querySelectorAll('select, input').forEach( field => {
            field.setAttribute('disabled', 'disabled')
        })
    }
    private loop(fields:Array<HTMLElement>, callback:(element:HTMLElement, field: HTMLInputElement|HTMLSelectElement) => void){
        fields.forEach(subField => {
            this.findFields(subField, (field) => {
                callback(subField, field)
            })
        })
    }
    private findFields(fieldsWrapper: HTMLElement, callback:(field: HTMLInputElement|HTMLSelectElement)=>void){
        fieldsWrapper.querySelectorAll('select, input').forEach(field => {
            callback(field as HTMLSelectElement|HTMLInputElement)
        });
    }
    private maybeToggleNegatedWrappers(negatedWrappers: Element[], value: string) {
        negatedWrappers.forEach( wrapper => {
            if(wrapper.getAttribute('data-repeat-frequency') !== `!${value}`){

                if(!wrapper.classList.contains('active')){
                    wrapper.classList.add('active');
                    this.enableWrapper(wrapper)
                }

                return;
            }
            if( wrapper.classList.contains('active')){
                wrapper.classList.remove('active')
                this.disableWrapper(wrapper);
            }
        })
    }

    private toggleWrappers(prevElements: NodeListOf<HTMLElement>, nextElements: NodeListOf<HTMLElement>) {
        const prevElementsArray = Array.from<HTMLElement>(prevElements);
        const nextElementsArray = Array.from<HTMLElement>(nextElements);

        this.loop(prevElementsArray, (wrapper, field) =>{
            wrapper.classList.remove('active')

            this.disableField(field)
        })
        this.loop(nextElementsArray, (wrapper, field) => {
            wrapper.classList.add('active');
            this.enableWrapper(wrapper);

        })

    }

    private updateElementsIds(cloneWrapper: HTMLElement) {
        cloneWrapper.querySelectorAll('[id]').forEach(element => {
            this.updateHtmlAttr( element, 'id');
        })
        cloneWrapper.querySelectorAll('label').forEach(label => {
            this.updateHtmlAttr( label, 'for')
        })

        return cloneWrapper;
    }

    private updateHtmlAttr(element: Element, key: string ) {

        const attribute = element.getAttribute(key);

        if(!attribute){
            return;
        }
        const isAlreadyNumbered = /-\d/.test(attribute);
        if (isAlreadyNumbered) {
            const attributeArr = attribute.split('-');
            const lastIndex = Number(attributeArr[attributeArr.length - 1]) + 1;
            attributeArr.pop()
            const newAttribute = attributeArr.join('-');
            element.setAttribute(key, `${newAttribute}-${lastIndex}`);
            return;
        }
        element.setAttribute(key, `${attribute}-2`)

    }

    private attachDateToField() {
        this.datePicker('.date', {
            dateFormat: "Y-m-d",
            defaultDate: new Date(),
            "disable": [
                (date) => {

                    // return true to disable
                    return (date.getTime() + (1000 * 60 * 60 * 24)) <= Date.now();

                }
            ],
        })
        this.datePicker('.date-time', {
            dateFormat: "Y-m-d H:i",
            enableTime: true,
            defaultDate: new Date(),
            "disable": [
                (date) => {
                    // return true to disable
                    return (date.getTime() + (1000 * 60 * 60 * 24)) <= Date.now();

                }
            ],
        })
    }
}
export default new ScheduleForms<typeof flatpickr>(flatpickr);