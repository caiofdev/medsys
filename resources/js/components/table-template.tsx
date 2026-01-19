import { useState } from "react"
import { Plus, Eye, PencilLine, Trash} from "lucide-react"
import {Dialog} from "@/components/ui/dialog"
import {ModalView, ModalEdit, ModalCreate, ModalDelete, ModalProvider} from "./modals"


interface User {
    id: number
    name: string
    email: string
    cpf: string
    phone: string
    is_master?: string
    photo: string | undefined;
    medical_history?: string
    birth_date?: Date | string
    emergency_contact?: string
    gender?: string 
    crm?: string
    register_number?: string
}

type UserRole = "admin" | "receptionist" | "doctor" | "patient";

interface TableProps {
    users: User[];
    type: UserRole;
}
export default function Table({ users, type}: TableProps) {

    const [open, setOpen] = useState(false);
    const [selectedUser, setSelectedUser] = useState<User | null>(null);
    const [operation, setOperation] = useState<"view" | "edit" | "delete" | "create" | null>(null);
    const [isLoadingDetails, setIsLoadingDetails] = useState(false);

    const handleAction = async (user: User | null, action: "view" | "edit" | "delete" | "create") => {
        setOperation(action);
        
        if (action === "view" && user) {
            setIsLoadingDetails(true);
            try {
                let detailsUrl = '';
                switch (type) {
                    case 'admin':
                        detailsUrl = `/admin/admins/${user.id}`;
                        break;
                    case 'doctor':
                        detailsUrl = `/admin/doctors/${user.id}`;
                        break;
                    case 'receptionist':
                        detailsUrl = `/admin/receptionists/${user.id}`;
                        break;
                    case 'patient':
                        detailsUrl = `/receptionist/patients/${user.id}`;
                        break;
                    default:
                        throw new Error('Tipo de usuário inválido');
                }

                const response = await fetch(detailsUrl, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
                    },
                });

                if (response.ok) {
                    const detailedUser = await response.json();
                    setSelectedUser(detailedUser);
                } else {
                    console.error('Erro ao buscar detalhes do usuário');
                    setSelectedUser(user);
                }
            } catch (error) {
                console.error('Erro ao buscar detalhes:', error);
                setSelectedUser(user);
            } finally {
                setIsLoadingDetails(false);
            }
        } else {
            setSelectedUser(user);
        }
        
        setOpen(true);
    };

    return (
        <div className="flex flex-col ml-25 mr-25 lg:ml-15 lg:mr-15 items-center justify-center">
            <div className="w-full flex flex-col space-y-2">
                <div className="flex w-full justify-end">
                    <button
                        className="flex items-center gap-2 bg-digital-blue-800 text-white px-5 py-2 rounded-lg shadow-md font-semibold text-sm hover:bg-digital-blue-700 hover:shadow-lg transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-digital-blue-400 cursor-pointer"
                        onClick={() => handleAction(null, "create")}
                    >
                        <Plus />
                        NOVO {type === "admin" ? "ADMINISTRADOR" : type === "receptionist" ? "RECEPCIONISTA" : type === "doctor" ? "DOUTOR" : "PACIENTE"}
                    </button>
                </div>
                
                <table className="w-full text-left items-center">  
                    <thead>
                        <tr className="flex bg-foreground text-lighttext font-bold mb-2 rounded-radius rounded-r-radius items-center justify-center">
                            <th className="w-full p-3">ID</th>
                            <th className="w-full p-3">Nome</th>
                            <th className="w-full p-3">E-mail</th>
                            <th className="flex w-full p-3 rounded-r-radius justify-center items-center">Operações</th>
                        </tr>
                    </thead>
                    <tbody>
                        {users.map((user) => (
                            <tr key={user.id} className="flex bg-digital-blue-50/70 hover:bg-digital-blue-100/60 border border-border rounded-radius mb-2 text-darktext">
                                <td className="w-full p-2 pl-4 font-medium rounded-l-radius">{user.id}</td>
                                <td className="w-full p-2 font-medium">{user.name}</td>
                                <td className="w-full p-2 font-medium">{user.email}</td>
                                <td className="w-full p-2 font-medium items-center justify-center rounded-r-radius">
                                    <div className="w-full flex gap-5 items-center justify-center">
                                        <button
                                            className="text-xl text-digital-blue-900 hover:text-digital-blue-700 transition duration-200 cursor-pointer"
                                            onClick={() => handleAction(user, "view")}
                                            title="Visualizar"
                                        >
                                            <Eye size={20} />
                                        </button>
                                        <button 
                                            className="text-xl text-digital-blue-900 hover:text-digital-blue-700 transition duration-200 cursor-pointer"
                                            onClick={() => handleAction(user, "edit")}
                                            title="Editar"
                                        >
                                            <PencilLine size={20} />
                                        </button>
                                        <button 
                                            className="text-xl text-error hover:text-red-500 transition duration-200 cursor-pointer"
                                            onClick={() => handleAction(user, "delete")}
                                            title="Deletar"
                                        >
                                            <Trash size={20} />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            
            <ModalProvider>
                <Dialog open={open} onOpenChange={setOpen} >
                    {operation === "view" && <ModalView user={selectedUser} type={type} />}
                    {operation === "edit" && <ModalEdit user={selectedUser} type={type} />}
                    {operation === "create" && <ModalCreate user={selectedUser} type={type} />}
                    {operation === "delete" && <ModalDelete user={selectedUser} type={type} />}
                </Dialog>
            </ModalProvider>
        </div>
    )
}
