export const PATHS_NAVIGATION = [
    {
        path:'#',
        title:'Link 1',
    },
    {
        path:'#',
        title:'Link 2',
    },
    {
        path:'#',
        title:'Link 3',
    }
]
export class Navigation extends HTMLElement {
    constructor(){
        super();
    }

    connectedCallback(){
        this.replaceChildren();

        const nav = window.document.createElement('nav');
        nav.classList.add('nav');

        PATHS_NAVIGATION.forEach(paths =>{
            const link = window.document.createElement('a');
            link.innerText = paths.title;
            link.setAttribute('href',paths.path);
            nav.appendChild(link);
        })

        this.appendChild(nav);
    }
}


window.customElements.define('jk-navigation',Navigation);