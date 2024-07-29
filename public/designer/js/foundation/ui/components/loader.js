class LoaderElement extends HTMLElement {
  constructor() {
    super();

    this.root = this.attachShadow({ mode: 'closed' });
  }

  connectedCallback() {
    this.root.innerHTML = `
      <style>
        :host {
          width: 8rem;
          height: 8rem;
          position: relative;
        }
        
        div {
          width: 8rem;
          height: 8rem;
          animation: dwl-dot-spin 5s infinite linear both;
          animation-delay: calc(var(--i) * 1s / 8 * -1);
          rotate: calc(var(--i) * 60deg / 7);
          position: absolute;
      
          &::before {
            content: '';
            display: block;
            width: 0.75rem;
            height: 0.75rem;
            background-color: var(--primary-color);
            border-radius: 50%;
            position: absolute;
            transform: translate(-50%, -50%);
            bottom: 0;
            left: 50%;
          }
        }
        
        @keyframes dwl-dot-spin {
          0% {
            transform: rotate(0deg);
            animation-timing-function: cubic-bezier(0.39, 0.575, 0.565, 1);
            opacity: 1;
          }
      
          2% {
            transform: rotate(20deg);
            animation-timing-function: linear;
            opacity: 1;
          }
      
          30% {
            transform: rotate(180deg);
            animation-timing-function: cubic-bezier(0.445, 0.05, 0.55, 0.95);
            opacity: 1;
          }
      
          41% {
            transform: rotate(380deg);
            animation-timing-function: linear;
            opacity: 1;
          }
      
          69% {
            transform: rotate(520deg);
            animation-timing-function: cubic-bezier(0.445, 0.05, 0.55, 0.95);
            opacity: 1;
          }
      
          76% {
            opacity: 1;
          }
      
          76.1% {
            opacity: 0;
          }
      
          80% {
            transform: rotate(720deg);
          }
      
          100% {
            opacity: 0;
          }
        }
      </style>
      <div style="--i: 0"></div>
      <div style="--i: 1"></div>
      <div style="--i: 2"></div>
      <div style="--i: 3"></div>
      <div style="--i: 4"></div>
      <div style="--i: 5"></div>`;
  }
}

if (!customElements.get('cms-loader')) {
  customElements.define('cms-loader', LoaderElement);
}
