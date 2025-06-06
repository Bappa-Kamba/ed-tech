<template>
  <div class="space-y-4 max-w-xl mx-auto">
    <div class="chat-history border rounded p-4 max-h-96 overflow-y-auto bg-gray-50">
      <div v-if="questions.length === 0" class="text-gray-500 italic">
        No questions asked yet.
      </div>
      <div v-for="(item, index) in questions" :key="index" class="mb-4">
        <div>
          <strong class="text-blue-700">You:</strong>
          <p class="whitespace-pre-wrap">{{ item.question }}</p>
        </div>
        <div class="mt-1">
          <strong class="text-green-700">AI Answer:</strong>
          <p class="whitespace-pre-wrap">{{ item.answer }}</p>
        </div>
        <hr class="mt-3" />
      </div>
    </div>

    <div>
      <label for="question" class="block font-semibold mb-1">Ask a question about this lesson:</label>
      <input
        v-model="question"
        type="text"
        id="question"
        class="w-full border px-4 py-2 rounded"
        @keyup.enter="submitQuestion"
        :disabled="loading"
        placeholder="Type your question here..."
      />
    </div>

    <button
      @click="submitQuestion"
      :disabled="loading || !question.trim()"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50"
    >
      {{ loading ? 'Thinking...' : 'Ask' }}
    </button>

    <div v-if="error" class="text-red-600 mt-2">
      {{ error }}
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    lessonId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      question: '',
      questions: [],
      loading: false,
      error: '',
    };
  },
  mounted() {
    this.fetchQuestions();
  },
  methods: {
    async fetchQuestions() {
      try {
        const res = await axios.get(`/lessons/${this.lessonId}/questions`);
        this.questions = res.data.questions || [];
      } catch (err) {
        // log error and show message
        console.error('Error fetching questions:', err);
        this.error = 'Failed to load previous questions.';
      }
    },
    async submitQuestion() {
      if (!this.question.trim()) return;

      this.error = '';
      this.loading = true;

      try {
        const res = await axios.post(`/lessons/${this.lessonId}/ask`, {
          question: this.question,
        });

        const answer = res.data.answer || 'Sorry, no answer returned.';
        
        // Add new Q&A to questions array
        this.questions.push({
          question: this.question,
          answer,
        });

        this.question = '';
      } catch (err) {
        this.error = err.response?.data?.message || 'Something went wrong.';
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.chat-history {
  /* Optional: to keep scroll at bottom on new message */
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
}
</style>
