// resources/js/app.js

import "./bootstrap";
import { createApp } from "vue";
import AskQuestion from "./components/AskQuestion.vue";
import LessonViewer from "./components/ShowLesson.vue";
import Alpine from "alpinejs";

// Start Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Create Vue app
const app = createApp({});

// Register global components
app.component("ask-question", AskQuestion);
app.component("lesson-show", LessonViewer);

// Mount to #app (must exist in Blade layout)
app.mount("#app");
