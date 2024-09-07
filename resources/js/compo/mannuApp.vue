<template>
    <div>
      <input
        type="text"
        v-model="newTask"
        placeholder="Enter a task"
        @keyup.enter="addTask"
      />
      <button class="btn btn-primary" @click="addTask">Add Task</button>
      <div class="col-md-4 offset-md-8 text-end">
      <button class="btn btn-primary" @click="showAllTasks">All Tasks</button>
    </div>
             
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Task</th>
            <th scope="col">status</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="tasks.length === 0">
            <td colspan="4">No tasks available</td>
          </tr>
          <tr v-for="(task, index) in filteredTasks" :key="task.id">
            <td>{{ index + 1 }}</td>
            <td>{{ task.title }}</td>
            <td>{{ task.completed === 'yes' ? 'Done' : 'No' }}</td>
            <td>
              <template v-if="task.completed === 'yes'">
                <button class="btn btn-xs btn-danger" @click="deleteTask(task.id)">
                <i class="bi bi-x-lg"></i>
                </button>
              </template>
              <template v-else>
                  <button class="btn btn-success" @click="completeTask(task.id)">
                    <i class="bi bi-check2-square"></i>
                 </button> | 
                <button class="btn btn-xs btn-danger" @click="deleteTask(task.id)">
                    <i class="bi bi-x-lg"></i>
                    </button>

              </template>
            </td>
          </tr>
        </tbody>
      </table>
      
    </div>
  </template>
  
  <script>
  
        import axios from 'axios';
        
        export default {
            data() {
            return {
                newTask: '',
                tasks: [],
                showAll: false,
            };
            },
            computed: {
            filteredTasks() {
                if (!this.tasks || this.tasks.length === 0) {
                return []; }

                return this.tasks.filter(task => {
                if (!task || !task.completed) {
                    return false; }
                return this.showAll || task.completed === 'no';
                });
            },
            },
            created() { this.fetchTasks(); },
            methods: {
            async fetchTasks() {
                try {
                const response = await axios.get('api/todo/list'); 
                this.tasks = response.data;
                } catch (error) {
                console.error('Error fetching tasks:', error);
                }
            },
             showAllTasks() {
                this.showAll = !this.showAll;               
            },
            async addTask() {
                if (this.newTask.trim() === '') return;              
                try {
                const response = await axios.post('api/todo/store', {
                    title: this.newTask,
                    completed: 'no',                    
                }); 
                console.log(response.data);                
                this.tasks.push(response.data);  
                this.newTask = '';
                } catch (error) {
                    if (error.response.status === 422) {
                        alert(error.response.data.message);
                    } 
                }
            },
            async completeTask(id) {
                const task = this.tasks.find(task => task.id === id);
                if (task) {
                try {
                    const response = await axios.put(`api/todo/edit/${id}`, 
                    { completed: task.completed === 'yes' ? 'no' : 'yes', });
                    task.completed = response.data.completed;  
                    } catch (error) {
                        console.error('Error marking task as complete:', error);
                    }
                }
            },
            async deleteTask(id) {
                if (confirm('Are you sure to delete this task?')) {
                try {
                    await axios.delete(`api/todo/destroy/${id}`); 
                    this.tasks = this.tasks.filter(task => task.id !== id);
                } catch (error) {
                    console.error('Error deleting task:', error);
                }
                }
            },
            },
        };
  </script>
  