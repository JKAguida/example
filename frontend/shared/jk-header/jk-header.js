import "../jk-navigation/jk-navigation.js";

export class Header extends HTMLElement {
    constructor() {
        super();
    }

    connectedCallback() {
        this.replaceChildren();

        const header = window.document.createElement('header');
        const logoContainer = window.document.createElement('div');
        const navigation = window.document.createElement('jk-navigation');
        const homeLink = window.document.createElement('a');
        const homeLinkStrong = window.document.createElement('strong');
        
        header.classList.add('layout__header');
        logoContainer.classList.add('logo')

        homeLink.setAttribute('href','/Auth/Me/me.html');
        homeLink.innerText = "JK";
        homeLinkStrong.innerText = "App";
        homeLink.appendChild(homeLinkStrong);

        logoContainer.append(homeLink);
        header.appendChild(logoContainer);
        header.appendChild(navigation);

        this.appendChild(header);
    }
}


window.customElements.define('jk-header', Header);