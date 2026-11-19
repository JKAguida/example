import "../jk-navigation/jk-navigation.js";


export class Footer extends HTMLElement {
    constructor(){
        super();
    }

    connectedCallback(){
        this.replaceChildren();
        
        const footer = window.document.createElement('footer');
        const navigation = window.document.createElement('jk-navigation');

        footer.classList.add('layout__footer');
        footer.appendChild(navigation);
        navigation.classList.add('footer__nav');
        this.appendChild(footer);
    }
}

window.customElements.define('jk-footer',Footer);