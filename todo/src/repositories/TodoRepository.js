import Repository from "./Repository";

const jsonHeader = {
	headers: {
		"Content-Type": "application/json"
	}
}

export default {
	getTodos () {
		return Repository.get("");
	},
	getTodo (id) {
		return Repository.get("/"+id);
	},
	createTodo (payload) {
		return Repository.post("/create", payload, jsonHeader);
	},
	updateTodo (id, payload) {
		return Repository.put("/update/"+id, payload, jsonHeader);
	},
	deleteTodo (id) {
		return Repository.delete("/delete/"+id);
	}
}
