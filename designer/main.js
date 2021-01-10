import init, { run_app } from './pkg/jinya_designer.js';
async function main() {
    await init('/pkg/jinya_designer_bg.wasm');
    run_app();
}
main()