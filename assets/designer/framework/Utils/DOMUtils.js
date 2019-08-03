import EventBus from '@/framework/Events/EventBus';
import Events from '@/framework/Events/Events';

export default {
    changeTitle(title) {
        document.title = `${window.messages.app.name} – ${title}`;
        EventBus.$emit(Events.header.change, title);
    },
};
