<template>
  <div class="flex flex-col h-full" :class="embedded ? 'p-0' : 'max-w-2xl mx-auto p-6 space-y-2'">
    
    <!-- Header Section (only show if not embedded) -->
    <div v-if="!embedded" class="glass rounded-2xl p-8 relative overflow-hidden hover-lift">
      <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-500/20 to-blue-600/20 rounded-full -translate-y-16 translate-x-16"></div>
        <div class="relative">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
              <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-question-circle text-2xl"></i>
              </div>
              <div>
                <h1 class="text-3xl font-bold text-black">AI Q&A Assistant</h1>
                <p class="text-gray-600">Ask questions and get instant answers about this lesson</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <button @click="toggleFullscreen" class="p-3 rounded-xl bg-white/60 hover:bg-white/80 transition-colors">
                <i :class="isFullscreen ? 'fas fa-compress' : 'fas fa-expand'" class="text-gray-400 text-lg"></i>
              </button>
              <button @click="clearChat" class="text-red-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 transition-colors">
                <i class="fas fa-trash"></i>
              </button>
              <button @click="exportChat" class="p-3 rounded-xl bg-white/60 hover:bg-white/80 transition-colors">
                <i class="fas fa-download text-gray-400 text-lg"></i>
              </button>
              <button @click="closeChat" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-times"></i>
              </button>
            </div>
        </div>
      </div>

    <!-- Chat Interface -->
    <div class="overflow-y-auto bg-gradient-to-b from-gray-50/30 to-white/30 p-6 chat-messages" 
     :style="{ height: embedded ? '400px' : (isFullscreen ? 'calc(100vh - 280px)' : '500px') }"
     ref="chatMessages">
      
      <!-- Chat Header -->
      <div class="p-1 border-b border-gray-200/80 bg-gradient-to-r from-green-50/80 to-blue-50/80 flex-shrink-0"
        :class="embedded ? 'p-4 rounded-none bg-white/80' : ''">
        
        <!-- Embedded Stats -->
        <div v-if="embedded" class="flex items-center justify-between text-xs text-gray-500 bg-white/60 rounded-lg p-3 mt-4">
          <div class="flex items-center space-x-4">
            <span class="flex items-center">
              <i class="fas fa-comments mr-1"></i>
              {{ questions.length }} messages
            </span>
            <span class="flex items-center">
              <i class="fas fa-thumbs-up mr-1"></i>
              {{ helpfulAnswers }} helpful
            </span>
          </div>
          <span class="text-green-600 font-medium">{{ sessionDuration }}m session</span>
        </div>
      </div>

      <!-- Chat Messages -->
      <div class="flex-1 overflow-y-auto bg-gray-400 p-6 chat-messages"
           :style="embedded ? {} : { height: isFullscreen ? 'calc(100vh - 280px)' : '500px' }"
           ref="chatMessages">
        
        <!-- Welcome Message -->
        <div v-if="questions.length === 0" class="flex items-start space-x-4 animate-fade-in">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gradient-to-br from-green-500 to-blue-600 flex-shrink-0 shadow-md">
            <i class="fas fa-robot text-blue-500 text-sm"></i>
          </div>
          <div class="flex-1" :class="embedded ? 'max-w-sm' : 'max-w-md'">
            <div class="bg-white rounded-2xl rounded-tl-lg px-4 py-3 shadow-sm border border-gray-800">
              <p class="text-sm text-gray-800">
                👋 Welcome! I'm here to help you understand this lesson better. Ask me anything about the concepts, 
                request examples, or test your knowledge. What would you like to know?
              </p>
            </div>
            <div class="flex items-center mt-2 text-xs text-gray-500">
              <i class="fas fa-clock mr-1"></i>
              <span>Just now</span>
            </div>
          </div>
        </div>

        <!-- Q&A History -->
        <div v-for="(item, index) in questions" :key="index" class="space-y-4">
          <!-- User Question -->
          <div class="flex items-start space-x-4 flex-row-reverse space-x-reverse animate-fade-in">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-600 to-gray-800 flex items-center justify-center flex-shrink-0 shadow-md">
              <i class="fas fa-user text-blue-600 text-sm"></i>
            </div>
            <div class="flex-1" :class="embedded ? 'max-w-sm' : 'max-w-md'">
              <div class="rounded-2xl rounded-tr-lg px-4 py-3 shadow-md">
                <p class="text-sm whitespace-pre-wrap">{{ item.question }}</p>
              </div>
              <div class="flex items-center mt-2 text-xs text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                <span>{{ item.created_at }}</span>
              </div>
            </div>
          </div>

          <!-- AI Answer -->
          <div class="flex items-start space-x-4 animate-fade-in">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gradient-to-br from-green-500 to-blue-600 flex-shrink-0 shadow-md">
              <i class="fas fa-robot text-blue-500 text-sm"></i>
            </div>
            <div class="flex-1" :class="embedded ? 'max-w-sm' : 'max-w-md'">
              <div class="bg-white rounded-2xl rounded-tl-lg px-4 py-3 shadow-sm border border-gray-600">
                <p class="text-sm text-gray-800 whitespace-pre-wrap" v-html="formatMessage(item.short_answer)"></p>
              </div>
              <div class="flex items-center justify-between mt-2">
                <div class="flex items-center text-xs text-gray-500">
                  <i class="fas fa-clock mr-1"></i>
                  <span>{{ item.created_at }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <button @click="rateAnswer(index, true)" 
                          :class="item.helpful === true ? 'text-green-500' : 'text-gray-400 hover:text-green-500'"
                          class="p-1 rounded transition-colors">
                    <i class="fas fa-thumbs-up text-xs"></i>
                  </button>
                  <button @click="rateAnswer(index, false)" 
                          :class="item.helpful === false ? 'text-red-500' : 'text-gray-400 hover:text-red-500'"
                          class="p-1 rounded transition-colors">
                    <i class="fas fa-thumbs-down text-xs"></i>
                  </button>
                  <button @click="copyAnswer(item.answer)" class="text-gray-400 hover:text-blue-500 p-1 rounded transition-colors">
                    <i class="fas fa-copy text-xs"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Typing Indicator -->
        <div v-if="loading" class="flex items-start space-x-4 animate-fade-in">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gradient-to-br from-green-500 to-blue-600 flex-shrink-0 shadow-md">
            <i class="fas fa-robot text-emerald-500 text-sm"></i>
          </div>
          <div class="flex-1" :class="embedded ? 'max-w-sm' : 'max-w-md'">
            <div class="bg-white rounded-2xl rounded-tl-lg px-4 py-3 shadow-sm border border-gray-100">
              <div class="flex items-center space-x-2">
                <div class="typing-dots">
                  <div class="dot"></div>
                  <div class="dot"></div>
                  <div class="dot"></div>
                </div>
                <span class="text-xs text-gray-500">AI is thinking...</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Input Section -->
      <div class="p-6 border-t border-gray-200 bg-white flex-shrink-0"
        :class="embedded ? 'p-4 rounded-none' : ''">
        <div class="flex items-end space-x-4">
          <div class="flex-1">
            <textarea
              v-model="currentQuestion"
              @keydown.enter.prevent="submitQuestion"
              @input="adjustTextareaHeight"
              ref="questionInput"
              placeholder="Ask a question about this lesson..."
              class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 resize-none transition-all duration-200 text-sm"
              :class="{ 'opacity-50': loading }"
              :disabled="loading"
              rows="1"
              style="min-height: 44px; max-height: 120px;"
            ></textarea>
          </div>
          <button
            @click="submitQuestion"
            :disabled="!currentQuestion.trim() || loading"
            class="p-3 rounded-xl bg-gradient-to-br from-green-500 to-blue-600 shadow-lg hover:shadow-xl transition-all duration-200 flex-shrink-0 disabled:opacity-50 disabled:cursor-not-allowed hover:scale-105"
          >
            <i v-if="!loading" class="fas fa-paper-plane text-sm"></i>
            <i v-else class="fas fa-spinner fa-spin text-sm"></i>
          </button>
        </div>
        
        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-2 mt-4">
          <button
            v-for="quickAction in quickActions"
            :key="quickAction"
            @click="useQuickAction(quickAction)"
            class="px-3 py-2 text-xs bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-gray-600 hover:text-gray-800"
          >
            {{ quickAction }}
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>
export default {
  name: 'AIQAAssistant',
  props: {
    embedded: {
      type: Boolean,
      default: false
    },
    lessonTitle: {
      type: String,
      default: ''
    },
    lessonContent: {
      type: String,
      default: ''
    },
    lessonId: {
      type: [Number, String],
      required: true
    }
  },
  data() {
    return {
      questions: [],
      currentQuestion: '',
      loading: false,
      sessionStartTime: Date.now(),
      isFullscreen: false,
      quickActions: [
        'Explain the main concept',
        'Give me an example',
        'Test my understanding',
        'Summarize key points',
        'What should I remember?'
      ]
    }
  },
  computed: {
    sessionDuration() {
      return Math.floor((Date.now() - this.sessionStartTime) / 60000)
    }
  },
  mounted() {
    this.fetchSavedQuestions();
  },
  methods: {
    async submitQuestion() {
      if (!this.currentQuestion.trim() || this.loading) return
      
      const question = this.currentQuestion.trim()
      this.currentQuestion = ''
      this.loading = true
      
      try {
          await this.getAIResponse(question);
          await this.fetchSavedQuestions();
      } catch (error) {
        console.error('Error getting AI response:', error)
        this.questions.push({
          question,
          answer: 'Sorry, I encountered an error. Please try again.',
          timestamp: this.formatTime(new Date()),
          responseTime: 0,
          helpful: null
        })
      } finally {
        this.loading = false
      }
    },
    
    async getAIResponse(question) {
    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      const res = await fetch(`/lessons/${this.lessonId}/ask`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ question })
      })

      if (!res.ok) throw new Error('API error')

      const data = await res.json()
      return;
    } catch (err) {
      console.error('Error calling backend AI endpoint:', err)
      return 'Sorry, something went wrong while getting the answer.'
    }
  },

  async fetchSavedQuestions() {
    try {
      const res = await fetch(`/lessons/${this.lessonId}/questions`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })

      if (!res.ok) throw new Error('Failed to fetch')

      const data = await res.json()
      // format created_at of each question
      this.questions = (data.questions || []).map(q => ({
        ...q,
        created_at: q.created_at
          ? new Date(q.created_at).toLocaleString([], { dateStyle: 'medium', timeStyle: 'short' })
          : ''
      }))

      this.$nextTick(() => {
        setTimeout(() => this.scrollToBottom(), 50);
      });
    } catch (err) {
      console.error('Failed to fetch saved questions:', err)
    }
  },
    
    useQuickAction(action) {
      this.currentQuestion = action
      this.submitQuestion()
    },
    
    rateAnswer(index, helpful) {
      this.questions[index].helpful = helpful
    },
    
    copyAnswer(answer) {
      navigator.clipboard.writeText(answer).then(() => {
        // Could add a toast notification here
        console.log('Answer copied to clipboard')
      })
    },
    
    clearChat() {
      if (confirm('Are you sure you want to clear the chat history?')) {
        this.questions = []
        this.sessionStartTime = Date.now()
      }
    },
    
    toggleFullscreen() {
      this.isFullscreen = !this.isFullscreen
    },
    
    exportChat() {
      const chatData = {
        lessonTitle: this.lessonTitle,
        sessionDuration: this.sessionDuration,
        questions: this.questions,
        exportTime: new Date().toISOString()
      }
      
      const blob = new Blob([JSON.stringify(chatData, null, 2)], { type: 'application/json' })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `qa-session-${Date.now()}.json`
      a.click()
      URL.revokeObjectURL(url)
    },

    closeChat() {
      this.$emit('close')
    },
    
    formatMessage(message) {
      if (!message) return;
      return message.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    },
    
    formatTime(date) {
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    },
    
    adjustTextareaHeight() {
      this.$nextTick(() => {
        const textarea = this.$refs.questionInput
        if (textarea) {
          textarea.style.height = 'auto'
          textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px'
        }
      })
    },
    
    scrollToBottom() {
      const chatMessages = this.$refs.chatMessages
      if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight
      }
    }
  }
}
</script>

<style scoped>
.glass {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.hover-lift {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.typing-dots {
  display: flex;
}

.typing-dots .dot {
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background-color: #9CA3AF;
  animation: typing 1.4s infinite ease-in-out;
  margin-right: 2px;
}

.typing-dots .dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dots .dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes typing {
  0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
  40% { transform: scale(1); opacity: 1; }
}

.chat-messages {
  scroll-behavior: smooth;
  flex-shrink: 0;
}

.chat-messages::-webkit-scrollbar {
  width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
  border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.3);
}
</style>
