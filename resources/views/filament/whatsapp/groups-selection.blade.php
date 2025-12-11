<div x-data="{
    groups: [],
    selectedGroups: [],
    selectedCount: 0,
    loading: false,
    saving: false,
    error: null,
    searchQuery: '',
    
    init() {
        this.fetchGroups();
    },
    
    get filteredGroups() {
        if (!this.searchQuery) return this.groups;
        const query = this.searchQuery.toLowerCase();
        return this.groups.filter(g => 
            (g.name || '').toLowerCase().includes(query) || 
            g.user.toLowerCase().includes(query)
        );
    },
    
    async fetchGroups() {
        this.loading = true;
        this.error = null;
        
        try {
            const response = await fetch('/whatsapp/groups', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.content
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.groups = data.groups || [];
                this.selectedGroups = data.selectedGroups || [];
                this.selectedCount = this.selectedGroups.length;
            } else {
                this.error = data.message ?? 'Falha ao obter lista de grupos';
            }
        } catch (err) {
            this.error = 'Erro ao conectar: ' + err.message;
        } finally {
            this.loading = false;
        }
    },
    
    async saveSelectedGroups() {
        this.saving = true;
        this.error = null;
        
        try {
            const response = await fetch('/whatsapp/groups/save', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.content
                },
                body: JSON.stringify({
                    groups: this.selectedGroups
                })
            });
            
            const data = await response.json();
            
            if (!data.success) {
                this.error = data.message ?? 'Falha ao salvar os grupos selecionados';
                return;
            }
            
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { message: 'Grupos salvos com sucesso!' }
            }));
            
        } catch (err) {
            this.error = 'Erro ao salvar: ' + err.message;
        } finally {
            this.saving = false;
        }
    },
    
    toggleGroup(group) {
        const index = this.selectedGroups.findIndex(g => g.user === group.user);
        if (index !== -1) {
            this.selectedGroups.splice(index, 1);
        } else {
            this.selectedGroups.push(group);
        }
        this.selectedCount = this.selectedGroups.length;
    },
    
    isSelected(group) {
        return this.selectedGroups.some(g => g.user === group.user);
    },
    
    selectAll() {
        this.selectedGroups = [...this.filteredGroups];
        this.selectedCount = this.selectedGroups.length;
    },
    
    deselectAll() {
        this.selectedGroups = [];
        this.selectedCount = 0;
    }
}" class="p-6">
    
    <!-- Header -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full mb-4">
            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">WhatsApp Groups</h3>
        <p class="text-gray-600">Select which groups should receive notifications</p>
    </div>
    
    <!-- LOADING -->
    <template x-if="loading">
        <div class="flex flex-col items-center justify-center py-16">
            <div class="relative">
                <div class="w-16 h-16 border-4 border-green-200 border-t-green-600 rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-8 h-8 bg-green-100 rounded-full"></div>
                </div>
            </div>
            <p class="mt-4 text-gray-600 font-medium">Loading groups...</p>
        </div>
    </template>
    
    <!-- ERROR -->
    <template x-if="!loading && error">
        <div class="bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-300 rounded-2xl p-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-200 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <p class="text-red-800 font-bold text-lg mb-2" x-text="error"></p>
            <button 
                @click="fetchGroups()"
                class="mt-4 inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-lg transition-all"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Try Again
            </button>
        </div>
    </template>
    
    <!-- GROUPS INTERFACE -->
    <template x-if="!loading && !error">
        <div class="space-y-5">
            <!-- Stats & Actions Bar -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-5 border-2 border-green-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center space-x-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600" x-text="groups.length"></div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Total Groups</div>
                        </div>
                        <div class="h-12 w-px bg-green-300"></div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-emerald-600" x-text="selectedCount"></div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Selected</div>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <button
                            @click="selectAll()"
                            class="px-4 py-2 bg-white border-2 border-green-300 text-green-700 font-semibold rounded-xl hover:bg-green-50 transition-all text-sm"
                        >
                            Select All
                        </button>
                        <button
                            @click="deselectAll()"
                            class="px-4 py-2 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all text-sm"
                        >
                            Clear
                        </button>
                        <button
                            @click="saveSelectedGroups()"
                            :disabled="saving"
                            class="px-6 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                        >
                            <svg x-show="!saving" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <svg x-show="saving" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="saving ? 'Saving...' : 'Save Selection'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input
                    type="text"
                    x-model="searchQuery"
                    placeholder="Search groups by name or ID..."
                    class="w-full pl-12 pr-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-0 transition-all"
                >
            </div>
            
            <!-- Groups List -->
            <div class="bg-white border-2 border-gray-200 rounded-2xl overflow-hidden shadow-lg" style="max-height: 50vh;">
                <div class="max-h-[50vh] overflow-y-auto">
                    <template x-if="filteredGroups.length === 0">
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="text-gray-500 font-medium">No groups found</p>
                        </div>
                    </template>
                    
                    <template x-for="group in filteredGroups" :key="group.user">
                        <div 
                            @click="toggleGroup(group)"
                            class="flex items-center px-6 py-4 border-b border-gray-100 hover:bg-green-50 cursor-pointer transition-all"
                            :class="isSelected(group) ? 'bg-green-50 border-l-4 border-l-green-600' : ''"
                        >
                            <div class="flex-shrink-0 mr-4">
                                <div class="relative">
                                    <input 
                                        type="checkbox" 
                                        :checked="isSelected(group)"
                                        @click.stop="toggleGroup(group)"
                                        class="w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 cursor-pointer transition-all"
                                    >
                                    <div x-show="isSelected(group)" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-bold text-gray-900 truncate" x-text="group.name || 'Unnamed Group'"></p>
                                    <span x-show="isSelected(group)" class="ml-2 px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded-full">
                                        Selected
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 font-mono" x-text="group.user"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>