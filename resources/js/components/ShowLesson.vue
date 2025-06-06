<template>
  <x-slot name="header">
  </x-slot>

  <!-- Reading Progress Indicator -->
  <div class="fixed top-20 right-4 bg-white/90 backdrop-blur-sm px-3 py-2 rounded-lg text-sm text-gray-600 z-30">
      <i class="fas fa-eye mr-1"></i>
      <span id="reading-progress">0%</span> read
  </div>

  <div class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" :class="{ 'lg:pr-80': chatOpen }">
              
              <!-- Main Content -->
              <div class="lg:col-span-2 space-y-6">
                  
                  <!-- Enhanced Lesson Progress -->
                  <div class="glass rounded-xl p-6 relative overflow-hidden"> 
                      <!-- Enhanced Stats Grid -->
                      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                          <div class="text-center p-3 bg-white/50 rounded-lg">
                              <div class="text-lg font-semibold text-gray-900">{{ timeSpent }}m</div>
                              <div class="text-xs text-gray-600">Time Spent</div>
                          </div>
                          <div class="text-center p-3 bg-white/50 rounded-lg">
                              <div class="text-lg font-semibold text-gray-900">{{ lesson.questions_count || 0 }}</div>
                              <div class="text-xs text-gray-600">Total Questions</div>
                          </div>
                          <div class="text-center p-3 bg-white/50 rounded-lg">
                              <div class="text-lg font-semibold text-gray-900">{{ readingProgress }}%</div>
                              <div class="text-xs text-gray-600">Content Read</div>
                          </div>
                      </div>
                  </div>

                  <!-- Lesson Content -->
                  <div class="glass rounded-xl p-6" id="lesson-content">
                      <div class="prose max-w-full lesson-content-formatted" v-html="lesson.content"></div>
                  </div>

                  <!-- Chat Suggestion Card -->
                  <div class="glass rounded-xl p-6">
                      <div class="flex items-center justify-between mb-4">
                          <h3 class="font-semibold text-gray-900 flex items-center">
                              <i class="fas fa-robot mr-2 text-blue-500"></i>
                              AI Learning Assistant
                          </h3>
                          <button @click="toggleChat" 
                                  class="btn-primary text-sm px-4 py-2 rounded-lg">
                              <i class="fas fa-comments mr-1"></i>
                              Start Learning Chat
                          </button>
                      </div>
                      <p class="text-gray-600 mb-4">
                          Get instant answers about {{ lesson.title.toLowerCase() }}! Ask questions, 
                          request explanations, or test your understanding.
                      </p>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Chat Toggle Button -->
  <button @click="toggleChat" 
      class="fixed bottom-6 right-6 w-14 h-14 rounded-full shadow-lg hover-lift z-40 transition-all duration-300"
      :class="chatOpen ? 'bg-red-500 hover:bg-red-600' : 'btn-primary'"
      style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
      <i :class="chatOpen ? 'fas fa-times' : 'fas fa-comments'" class="text-white text-xl"></i>
  </button>

  <!-- Chat Panel -->
  <div class="fixed right-0 bg-white/95 backdrop-blur-lg border-l border-white/20 shadow-2xl z-50 transform transition-all duration-300 flex flex-col chat-panel"
        :class="[chatOpen ? 'translate-x-0' : 'translate-x-full', isResizing ? 'resizing' : '']"
        :style="{ 
            top: '80px', 
            height: 'calc(100vh - 80px)', 
            width: chatWidth + 'px' 
        }"
        ref="chatPanel">
      
      <!-- Resize Handle -->
      <div class="resize-handle" 
        @mousedown="startResize"
        ref="resizeHandle">
      </div>
      
      <!-- Chat Header -->
      <div class="p-4 border-b border-gray-200/50 bg-white/80">
          <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                      <i class="fas fa-robot text-white"></i>
                  </div>
                  <div>
                      <h3 class="font-semibold text-gray-900">AI Tutor</h3>
                      <p class="text-xs text-gray-600">Ask me about {{ lesson.title }}</p>
                  </div>
              </div>
              <button @click="toggleChat" class="close-btn-prominent text-red-500 hover:text-red-600">
                  <i class="fas fa-times text-lg"></i>
              </button>
          </div>
          
          <!-- Width indicator (optional) -->
          <div class="mt-2 text-xs text-gray-400 flex items-center space-x-2">
              <i class="fas fa-expand-arrows-alt"></i>
              <span>{{ chatWidth }}px width - Drag left edge to resize</span>
          </div>
      </div>

      <!-- Chat Messages -->
      <div class="flex-1 overflow-y-auto p-4 space-y-4" ref="chatMessages">
          <!-- Welcome Message -->
          <div class="flex items-start space-x-3">
              <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gradient-to-r from-blue-500 to-purple-600 flex-shrink-0">
                  <i class="fas fa-robot text-white text-sm"></i>
              </div>
              <div class="flex-1">
                  <div class="bg-gray-100 rounded-2xl rounded-tl-sm px-4 py-2">
                      <p class="text-sm text-gray-800">
                          Hi! I'm here to help you understand "{{ lesson.title }}". 
                          Ask me anything about this lesson!
                      </p>
                  </div>
                  <span class="text-xs text-gray-500 ml-2">Just now</span>
              </div>
          </div>

          <!-- Dynamic Messages -->
          <div v-for="message in chatMessages" :key="message.id" 
                class="flex items-start space-x-3"
                :class="message.isUser ? 'flex-row-reverse space-x-reverse' : ''">
              
              <div v-if="!message.isUser" class="w-8 h-8 rounded-full flex items-center justify-center bg-gradient-to-r from-blue-500 to-purple-600 flex-shrink-0">
                  <i class="fas fa-robot text-white text-sm"></i>
              </div>
              <div v-else class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center flex-shrink-0">
                  <i class="fas fa-user text-gray-600 text-sm"></i>
              </div>

              <div class="flex-1 max-w-xs">
                  <div class="rounded-2xl px-4 py-2 break-words"
                        :class="message.isUser ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-tr-sm' : 'bg-gray-100 text-gray-800 rounded-tl-sm'">
                      <p class="text-sm" v-html="message.content"></p>
                  </div>
                  <span class="text-xs text-gray-500 ml-2">{{ message.timestamp }}</span>
              </div>
          </div>

          <!-- Typing Indicator -->
          <div v-if="isTyping" class="flex items-start space-x-3">
              <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gradient-to-r from-blue-500 to-purple-600 flex-shrink-0">
                  <i class="fas fa-robot text-white text-sm"></i>
              </div>
              <div class="bg-gray-100 rounded-2xl rounded-tl-sm px-4 py-2">
                  <div class="flex space-x-1">
                      <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                      <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                      <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Chat Input -->
      <div class="p-4 border-t border-gray-200/50 bg-white/80">
          <div class="flex space-x-2">
              <input 
                  v-model="newMessage"
                  @keyup.enter="sendMessage"
                  type="text" 
                  :placeholder="`Ask about ${lesson.title}...`"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/80"
              >
              <button @click="sendMessage" 
                      :disabled="!newMessage.trim() || isTyping"
                      class="btn-primary px-4 py-2 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                  <i class="fas fa-paper-plane"></i>
              </button>
          </div>
      </div>
  </div>
</template>

<script setup>
    import { ref, reactive, onMounted, nextTick } from 'vue'
    import axios from 'axios'

    // Define props to receive lesson data
    const props = defineProps({
      lesson: {
        type: Object,
        required: true
      }
    })

    const chatOpen = ref(false)
    const newMessage = ref('')
    const isTyping = ref(false)
    const startTime = Date.now()
    const timeSpent = ref(0)
    const readingProgress = ref(0)
    const progressPercentage = ref(0)
    const chatMessagesRef = ref(null)
    const chatMessages = reactive([])
    const chatPanel = ref(null)
    const resizeHandle = ref(null)
    const isResizing = ref(false)
    const chatWidth = ref(400) // Initial chat panel width

    function startResize(e) {
      isResizing.value = true
      const startX = e.clientX
      const startWidth = chatWidth.value

      const onMouseMove = (moveEvent) => {
        const delta = startX - moveEvent.clientX
        const newWidth = Math.min(Math.max(startWidth + delta, 300), 800)
        chatWidth.value = newWidth
      }

      const onMouseUp = () => {
        isResizing.value = false
        window.removeEventListener('mousemove', onMouseMove)
        window.removeEventListener('mouseup', onMouseUp)
      }

      window.addEventListener('mousemove', onMouseMove)
      window.addEventListener('mouseup', onMouseUp)
    }

    function toggleChat() {
      chatOpen.value = !chatOpen.value
      if (chatOpen.value) {
        nextTick(() => {
          if (chatMessagesRef.value) {
            chatMessagesRef.value.scrollTop = chatMessagesRef.value.scrollHeight
          }
        })
      }
    }

    // Within <script setup>
    async function sendMessage() {
      if (!newMessage.value.trim()) return;

      const userText = newMessage.value.trim();
      chatMessages.value.push({
        id: Date.now(),
        content: userText,
        isUser: true,
        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
      });

      newMessage.value = '';
      isTyping.value = true;

      try {
        const response = await fetch('/api/ai-chat', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            lesson_id: props.lesson.id,
            message: userText
          })
        });

        const { reply } = await response.json();

        chatMessages.value.push({
          id: Date.now() + 1,
          content: reply || 'Sorry, no answer.',
          isUser: false,
          timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        });
      } catch (e) {
        chatMessages.value.push({
          id: Date.now() + 2,
          content: 'Something went wrong. Try again later.',
          isUser: false,
          timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        });
      } finally {
        isTyping.value = false;
        await nextTick();
        chatMessagesRef.value.scrollTop = chatMessagesRef.value.scrollHeight;
      }
    }


    function initializeProgressTracking() {
      window.addEventListener('scroll', () => {
        const lessonContent = document.getElementById('lesson-content')
        if (!lessonContent) return

        const rect = lessonContent.getBoundingClientRect()
        const windowHeight = window.innerHeight
        const contentHeight = lessonContent.scrollHeight

        const scrolled = window.scrollY + windowHeight
        const contentTop = lessonContent.offsetTop
        const contentBottom = contentTop + contentHeight

        if (scrolled > contentTop && scrolled < contentBottom) {
          readingProgress.value = Math.round(
            Math.min(100, Math.max(0, ((scrolled - contentTop) / contentHeight) * 100))
          )
        } else if (scrolled >= contentBottom) {
          readingProgress.value = 100
        }

        const progressElement = document.getElementById('reading-progress')
        if (progressElement) {
          progressElement.textContent = readingProgress.value + '%'
        }

        calculateProgress()
      })
    }

    function updateTimeSpent() {
      timeSpent.value = Math.floor((Date.now() - startTime) / 60000)
      calculateProgress()
    }

    const fetchChatMessages = async (lessonId) => {
      try {
        const { data } = await axios.get(`/lessons/${lessonId}/questions`)
        // Optional: format or sort messages here if needed
        chatMessages.value = data.questions || []
      } catch (error) {
        console.error('Failed to fetch chat messages:', error)
      }
    }

    function calculateProgress() {
      const readingWeight = Math.min(readingProgress.value * 0.4, 40)
      const timeWeight = Math.min(timeSpent.value * 2, 30)

      progressPercentage.value = Math.min(100, Math.round(20 + readingWeight + timeWeight))
    }

    onMounted(() => {
      initializeProgressTracking()
      updateTimeSpent()
      setInterval(updateTimeSpent, 60000)
      fetchChatMessages(props.lesson.id)
    })
</script>

<style scoped>
  .lesson-content-formatted {
    text-align: justify;
    line-height: 2;
    letter-spacing: 0.060em;
    overflow-wrap: break-word;
    word-break: break-word;
    white-space: normal;
  }

  /* Target specific elements within the content */
  .lesson-content-formatted p {
    text-align: justify;
    margin-bottom: 1rem;
  }

  .lesson-content-formatted h1,
  .lesson-content-formatted h2,
  .lesson-content-formatted h3,
  .lesson-content-formatted h4 {
    text-align: left; /* Keep headings left-aligned */
  }

  .lesson-content-formatted ul,
  .lesson-content-formatted ol {
    text-align: left; /* Keep lists left-aligned */
  }

  .resize-handle {
    position: absolute;
    left: 0;
    top: 0;
    width: 6px;
    height: 100%;
    cursor: ew-resize;
    z-index: 10;
  }
  .chat-panel.resizing {
    user-select: none;
  }
</style>
