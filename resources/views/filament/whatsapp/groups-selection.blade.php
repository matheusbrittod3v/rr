<div x-data="{
    groups: [],
    selectedGroups: [],
    selectedCount: 0,
    loading: false,
    saving: false,
    error: null,
    
    init() {
        this.fetchGroups();
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
            
            // Dispatch a notification event
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
    }
}" class="space-y-5">
    
    <!-- LOADING -->
    <template x-if="loading">
        <div class="flex flex-col items-center justify-center py-10">
            <div class="animate-spin h-10 w-10 border-4 border-blue-500 border-t-transparent rounded-full"></div>
            <p class="mt-3 text-gray-700">Carregando...</p>
        </div>
    </template>
    
    <!-- ERRO -->
    <template x-if="!loading && error">
        <div class="bg-red-50 border border-red-300 p-4 rounded-lg text-center">
            <p class="text-red-700 font-medium" x-text="error"></p>
            <button 
                @click="fetchGroups()"
                class="mt-3 px-3 py-1.5 text-xs bg-red-600 text-white rounded-md hover:bg-red-700"
            >
                Tentar novamente
            </button>
        </div>
    </template>
    
    <!-- HEADER -->
    <template x-if="!loading && !error">
        <div class="bg-gray-100 p-3 rounded-lg flex justify-between items-center">
            <div>
                <p class="font-semibold text-gray-800">
                    <span x-text="groups.length"></span> grupos encontrados
                </p>
                <p class="text-sm text-gray-600">
                    <span x-text="selectedCount"></span> selecionados
                </p>
            </div>
            
            <button
                @click="saveSelectedGroups"
                :disabled="saving"
                class="px-4 py-2 bg-green-600 text-white rounded-md shadow hover:bg-green-700 disabled:opacity-50 flex items-center"
            >
                <span x-show="!saving">Salvar Seleção</span>
                <span x-show="saving" class="flex items-center">
                    <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                        <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.3 0 0 5.3 0 12h4z" class="opacity-75"></path>
                    </svg>
                    Salvando...
                </span>
            </button>
        </div>
    </template>
    
    <!-- LISTA DE GRUPOS -->
    <template x-if="!loading && !error && groups.length > 0">
        <div class="border border-gray-200 rounded-lg overflow-hidden max-h-[50vh] overflow-y-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Selecionar
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nome do Grupo
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID do Grupo
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="group in groups" :key="group.user">
                        <tr 
                            @click="toggleGroup(group)"
                            class="cursor-pointer hover:bg-gray-50"
                            :class="isSelected(group) ? 'bg-blue-50' : ''"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        :checked="isSelected(group)"
                                        class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500"
                                        @click.stop="toggleGroup(group)"
                                    >
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900" x-text="group.name ?? 'Sem nome'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500" x-text="group.user"></div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </template>
</div>