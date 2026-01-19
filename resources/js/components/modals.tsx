import { useState, useEffect, createContext, useContext, ReactNode, useRef } from "react"
import { useInitials } from '@/hooks/use-initials';
import { InputField } from "./input-field";
import { SelectField } from "./select-field";
import { faUser, faEnvelope, faIdCard, faPhone, faGear, faIdCardClip, faCommentMedical, faCalendar, faKey} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogClose } from "@/components/ui/dialog";
import { Avatar, AvatarImage, AvatarFallback } from "@/components/ui/avatar";
import InputError from "./input-error";
import { router } from '@inertiajs/react';


const validateUserData = (formData: any, type: string, isEdit: boolean = false, fileInput?: HTMLInputElement | null) => {
    const errors: string[] = [];
    
    if (!formData.name || formData.name.trim() === '') {
        errors.push("Nome é obrigatório");
    }
    
    if (!formData.email || formData.email.trim() === '') {
        errors.push("E-mail é obrigatório");
    } else {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.email)) {
            errors.push("E-mail deve ter um formato válido");
        }
    }
    
    if (!isEdit) {
        if (!formData.cpf || formData.cpf.trim() === '') {
            errors.push("CPF é obrigatório");
        } else {
            const cleanCpf = formData.cpf.replace(/\D/g, '');
            if (cleanCpf.length !== 11) {
                errors.push("CPF deve ter 11 dígitos");
            }
        }
    }
    
    if (!formData.phone || formData.phone.trim() === '') {
        errors.push("Telefone é obrigatório");
    } else if (formData.phone.length > 20) {
        errors.push("Telefone deve ter no máximo 20 caracteres");
    }
    
    if (!isEdit && type !== "patient") {
        if (!formData.password || formData.password.trim() === '') {
            errors.push("Senha é obrigatória");
        } else if (formData.password.length < 6) {
            errors.push("Senha deve ter pelo menos 6 caracteres");
        }
    }
    
    if (!isEdit) {
        if (!formData.birth_date) {
            errors.push("Data de nascimento é obrigatória");
        }
    }

    if (type === "admin" && !isEdit) {
        if (!formData.is_master || !['yes', 'no'].includes(formData.is_master)) {
            errors.push("Administrador Master deve ser 'Sim' ou 'Não'");
        }
    }

    if (type === "doctor" && !isEdit) {
        if (!formData.crm || formData.crm.trim() === '') {
            errors.push("CRM é obrigatório");
        }
    }

    if (type === "receptionist" && !isEdit) {
        if (!formData.register_number || formData.register_number.trim() === '') {
            errors.push("Número de registro é obrigatório");
        }
    }

    if (type === "patient" && !isEdit) {
        if (!formData.gender || formData.gender === "Selecione o gênero") {
            errors.push("Gênero é obrigatório");
        }
        if (!formData.emergency_contact || formData.emergency_contact.trim() === '') {
            errors.push("Contato de emergência é obrigatório");
        }
    }

    if (fileInput?.files?.[0]) {
        const file = fileInput.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        const maxSize = 2048 * 1024;
        
        if (!allowedTypes.includes(file.type)) {
            errors.push("Foto deve ser do tipo: JPEG, PNG, JPG ou GIF");
        }
        
        if (file.size > maxSize) {
            errors.push("Foto deve ter no máximo 2MB");
        }
    }

    return errors;
};


export interface User {
    id: number
    name: string
    email: string
    cpf: string
    phone: string
    photo: string | undefined;
    is_master?: string
    medical_history?: string
    birth_date?: Date | string
    emergency_contact?: string
    gender?: string
    crm?: string
    register_number?: string
}


type ModalType = "view" | "edit" | "create";

type UserRole = "admin" | "receptionist" | "doctor" | "patient";

interface ModalProps {
    user: User | null;
    type: UserRole;
}

interface ModalContextType {

    preview: string;
    gender: string;
    is_master: string;

    formData: {
        name: string;
        email: string;
        cpf: string;
        phone: string;
        photo: string;
        is_master: string;
        medical_history: string;
        birth_date: string;
        emergency_contact: string;
        gender: string;
        crm: string;
        register_number: string;
        password: string;
    };

    appointmentFormData: {
        patient: User
        doctor: User
        date: string
        time: string
        price: number
        status: string
    };

    searchQuery: string;
    filteredPatients: User[];
    selectedPatient: User | null;
    doctorQuery: string;
    filteredDoctors: User[];
    selectedDoctor: User | null;

    setPreview: (value: string) => void;
    setGender: (value: string) => void;
    setIsMaster: (value: string) => void;
    setFormData: React.Dispatch<React.SetStateAction<any>>;
    setAppointmentFormData: React.Dispatch<React.SetStateAction<any>>;
    
    setSearchQuery: (value: string) => void;
    setFilteredPatients: (value: User[]) => void;
    setSelectedPatient: (value: User | null) => void;
    setDoctorQuery: (value: string) => void;
    setFilteredDoctors: (value: User[]) => void;
    setSelectedDoctor: (value: User | null) => void;

    handleChange: (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => void;
    handleImageChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
    handleSelectChange: (e: React.ChangeEvent<HTMLSelectElement>) => void;
    handleAppointmentChange: (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => void;

    handlePatientSelect: (patient: User) => void;
    handleDoctorSelect: (doctor: User) => void;
    handleCreateAppointment: () => void;

    searchPatients: (query: string) => Promise<void>;
    searchDoctors: (query: string) => Promise<void>;

    resetFormData: () => void;
    resetAppointmentData: () => void;
    initializeEditMode: (user: User) => void;
}

const ModalContext = createContext<ModalContextType | undefined>(undefined);

function ModalProvider({ children }: { children: ReactNode }) {
    const [preview, setPreview] = useState("");
    const [gender, setGender] = useState("Selecione o gênero");
    const [is_master, setIsMaster] = useState("Selecione a opção");
    
    const [formData, setFormData] = useState({
        name: "",
        email: "",
        cpf: "",
        phone: "",
        photo: "",
        is_master: "",
        medical_history: "",
        birth_date: "",
        emergency_contact: "",
        gender: "",
        crm: "",
        register_number: "",
        password: "",
    });
    
    const [appointmentFormData, setAppointmentFormData] = useState({
        patient: {} as User,
        doctor: {} as User,
        date: new Date().toISOString().split('T')[0],
        time: "",
        price: 0,
        status: "scheduled",
    });

    const [searchQuery, setSearchQuery] = useState("");
    const [filteredPatients, setFilteredPatients] = useState<User[]>([]);
    const [selectedPatient, setSelectedPatient] = useState<User | null>(null);
    const [doctorQuery, setDoctorQuery] = useState("");
    const [filteredDoctors, setFilteredDoctors] = useState<User[]>([]);
    const [selectedDoctor, setSelectedDoctor] = useState<User | null>(null);

    // Verificar se o token CSRF está disponível quando o componente é montado
    useEffect(() => {
        const checkCsrfToken = () => {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                console.error('CSRF token não encontrado no carregamento inicial');
                // Tentar aguardar um pouco e verificar novamente
                setTimeout(() => {
                    const retryToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (!retryToken) {
                        console.error('CSRF token ainda não está disponível após delay');
                    }
                }, 1000);
            }
        };

        // Verificar imediatamente e após um delay
        checkCsrfToken();
    }, []);
    
    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        const { name, value } = e.target;
        setFormData((prev) => ({ ...prev, [name]: value }));
    };

    const handleAppointmentChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        const { name, value } = e.target;
        
        if (name === 'price') {
            const numericValue = value.replace(/[^\d.,]/g, '');
            const normalizedValue = numericValue.replace(',', '.');
            const numberValue = parseFloat(normalizedValue) || 0;
            setAppointmentFormData((prev) => ({ ...prev, [name]: numberValue }));
        } else {
            setAppointmentFormData((prev) => ({ ...prev, [name]: value }));
        }
    };

    const formatPrice = (price: number): string => {
        return price.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    const formatPriceForInput = (price: number): string => {
        if (price === 0) return '';
        return price.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) setPreview(URL.createObjectURL(file));
    };
    
    const handleSelectChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        const { name, value } = e.target;
        if (name === "gender") {
            setGender(value);
            setFormData((prev) => ({ ...prev, gender: value }));
        }
        if (name === "is_master") {
            setIsMaster(value);
            setFormData((prev) => ({ ...prev, is_master: value }));
        }
    };

    const handlePatientSelect = (patient: User) => {
        setSelectedPatient(patient);
        setAppointmentFormData((prev) => ({ ...prev, patient }));
        setSearchQuery(patient.name);
        setFilteredPatients([]);
    };

    const handleDoctorSelect = (doctor: User) => {
        setSelectedDoctor(doctor);
        setAppointmentFormData((prev) => ({ ...prev, doctor }));
        setDoctorQuery(doctor.name);
        setFilteredDoctors([]);
    };

    const handleCreateAppointment = () => {
        
        if (!appointmentFormData.patient?.id || !appointmentFormData.doctor?.id || !appointmentFormData.date || !appointmentFormData.time || appointmentFormData.price <= 0) {
            alert('Por favor, preencha todos os campos obrigatórios');
            return;
        }

        console.log('Enviando dados para agendamento via Inertia...');
        console.log('Dados:', {
            patient_id: appointmentFormData.patient.id,
            doctor_id: appointmentFormData.doctor.id,
            date: appointmentFormData.date,
            time: appointmentFormData.time,
            price: appointmentFormData.price,
            status: appointmentFormData.status || 'scheduled',
        });

        router.post('/receptionist/appointments', {
            patient_id: appointmentFormData.patient.id,
            doctor_id: appointmentFormData.doctor.id,
            date: appointmentFormData.date,
            time: appointmentFormData.time,
            price: appointmentFormData.price,
            status: appointmentFormData.status || 'scheduled',
        }, {
            onSuccess: (page) => {
                console.log('Sucesso:', page);
                alert(`Consulta agendada com sucesso para ${appointmentFormData.patient.name} com ${appointmentFormData.doctor.name}`);
                resetAppointmentData();
                window.location.reload();
            },
            onError: (errors) => {
                console.error('Erro:', errors);
                
                // Verifica se há uma mensagem de erro específica
                if (errors.message) {
                    alert(errors.message);
                } else if (Object.keys(errors).length > 0) {
                    // Pega o primeiro erro disponível
                    const firstError = Object.values(errors)[0];
                    alert(Array.isArray(firstError) ? firstError[0] : firstError);
                } else {
                    alert('Erro ao agendar consulta. Tente novamente.');
                }
            },
            onFinish: () => {
                console.log('Requisição finalizada');
            }
        });
    };

    const searchPatients = async (query: string) => {
        if (!query.trim()) {
            setFilteredPatients([]);
            return;
        }

        try {
            const response = await fetch(`/receptionist/appointments/patients?q=${encodeURIComponent(query)}`, {
                headers: {
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    setFilteredPatients(data.patients);
                }
            } else {
                console.error('Erro ao buscar pacientes:', response.status);
            }
        } catch (error) {
            console.error('Erro ao buscar pacientes:', error);
        }
    };

    const searchDoctors = async (query: string) => {
        if (!query.trim()) {
            setFilteredDoctors([]);
            return;
        }

        try {
            const response = await fetch(`/receptionist/appointments/doctors?q=${encodeURIComponent(query)}`, {
                headers: {
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    setFilteredDoctors(data.doctors);
                }
            } else {
                console.error('Erro ao buscar médicos:', response.status);
            }
        } catch (error) {
            console.error('Erro ao buscar médicos:', error);
        }
    };
    
    const resetFormData = () => {
        setFormData({
            name: "",
            email: "",
            cpf: "",
            phone: "",
            photo: "",
            is_master: "",
            medical_history: "",
            birth_date: "",
            emergency_contact: "",
            gender: "",
            crm: "",
            register_number: "",
            password: "",
        });
        setPreview("");
        setGender("Selecione o gênero");
        setIsMaster("Selecione a opção");
    };
    
    const initializeEditMode = (user: User) => {
        setFormData({
            name: user.name,
            email: user.email,
            cpf: user.cpf,
            phone: user.phone,
            photo: user.photo || "",
            is_master: typeof user.is_master === 'boolean' 
                ? (user.is_master ? "yes" : "no")
                : (user.is_master || ""),
            medical_history: user.medical_history || "",
            birth_date: user.birth_date?.toString() || "",
            emergency_contact: user.emergency_contact || "",
            gender: user.gender || "",
            crm: user.crm || "",
            register_number: user.register_number || "",
            password: "",
        });
        
        setPreview(user.photo || "");
        
        // Sincronizar o estado gender com formData.gender para compatibilidade
        setGender(user.gender || "");
        
        setIsMaster(
            typeof user.is_master === 'boolean' 
                ? (user.is_master ? "yes" : "no")
                : (user.is_master || "Selecione a opção")
        );
    };
    
    const resetAppointmentData = () => {
        setAppointmentFormData({
            patient: {} as User,
            doctor: {} as User,
            date: new Date().toISOString().split('T')[0],
            time: "",
            price: 0,
            status: "scheduled",
        });
        setSelectedPatient(null);
        setSearchQuery("");
        setFilteredPatients([]);
        setSelectedDoctor(null);
        setDoctorQuery("");
        setFilteredDoctors([]);
    };

    const value = {
        preview,
        gender,
        is_master,
        formData,
        appointmentFormData,
        
        searchQuery,
        filteredPatients,
        selectedPatient,
        doctorQuery,
        filteredDoctors,
        selectedDoctor,
        
        setPreview,
        setGender,
        setIsMaster,
        setFormData,
        setAppointmentFormData,
        
        setSearchQuery,
        setFilteredPatients,
        setSelectedPatient,
        setDoctorQuery,
        setFilteredDoctors,
        setSelectedDoctor,
        
        handleChange,
        handleImageChange,
        handleSelectChange,
        handleAppointmentChange,
        
        handlePatientSelect,
        handleDoctorSelect,
        handleCreateAppointment,
        
        searchPatients,
        searchDoctors,
        
        resetFormData,
        resetAppointmentData,
        initializeEditMode,
    };
    
    return (
        <ModalContext.Provider value={value}>
            {children}
        </ModalContext.Provider>
    );
}

function useModal() {
    const context = useContext(ModalContext);
    if (context === undefined) {
        throw new Error('useModal must be used within a ModalProvider');
    }
    return context;
}

function ModalView({ user, type }: ModalProps)  {
    const getInitials = useInitials();

    return (
        <DialogContent className="bg-foreground p-0 pt-3 rounded-2xl overflow-y-auto">
        <DialogHeader>
            <DialogTitle className="text-white text-center p-2">Detalhes de {user ? user.name : type === "admin" ? "Administrador" : type === "receptionist" ? "Recepcionista" : type === "doctor" ? "Doutor" : "Paciente"}</DialogTitle>
            <DialogDescription className=" flex-col max-h-[86vh] bg-white-50 p-4 rounded-b-2xl space-y-4 text-darktext overflow-y-auto flex-1 custom-scrollbar">
            {user ? (
                <>
                    {type !== "patient" && (
                        <div className="flex justify-center">
                            <Avatar className="h-22 w-22 rounded-full">
                                <AvatarImage
                                    src={user.photo}
                                    alt={user.name}
                                    className="object-cover w-full h-full rounded-full"
                                />
                                <AvatarFallback className="text-2xl">
                                    {getInitials(user.name)}
                                </AvatarFallback>
                            </Avatar>
                        </div>
                    )}
                <InputField label="Nome" icon={<FontAwesomeIcon icon={faUser} />} value={user.name} disabled />
                <InputField label="E-mail" icon={<FontAwesomeIcon icon={faEnvelope} />} value={user.email} disabled />
                <div className="flex gap-3">
                    <InputField label="CPF" icon={<FontAwesomeIcon icon={faIdCard} />} value={user.cpf} disabled />
                    <InputField label="Telefone" icon={<FontAwesomeIcon icon={faPhone} />} value={user.phone} disabled />
                </div>
                <InputField label="Data de Nascimento" icon={<FontAwesomeIcon icon={faCalendar} />} value={
                    user.birth_date 
                        ? (typeof user.birth_date === 'string' 
                            ? new Date(user.birth_date).toLocaleDateString() 
                            : user.birth_date.toLocaleDateString())
                        : ""
                } disabled />

                { type=="doctor" && (
                <div className="flex gap-3">
                    <InputField label="CRM" icon={""} value={user.crm ?? ""} disabled />
                </div>
                )}

                { type=="admin" && (
                    <InputField
                        label="Administrador Master"
                        icon={<FontAwesomeIcon icon={faGear} />}
                        value={
                            (typeof user.is_master === 'boolean' && user.is_master) || 
                            user.is_master === 'Sim' || 
                            user.is_master === 'yes' || 
                            user.is_master === '1'
                                ? "Sim" 
                                : "Não"
                        }
                        disabled
                    />
                )}

                { type=="receptionist" && (
                    <InputField
                        label="Número de Registro"
                        icon={<FontAwesomeIcon icon={faIdCardClip} />}
                        value={user.register_number ?? ""}
                        disabled
                    />
                )}

                { type=="patient" && (
                    <div className="flex flex-col gap-3">
                        <div className="flex gap-3">
                            <InputField label="Gênero" icon={""} value={user.gender === 'male' ? 'Masculino' : 'Feminino'} disabled />
                        </div>
                            <InputField
                                label="Contato de Emergência"
                                icon={<FontAwesomeIcon icon={faCommentMedical} />}
                                value={user.emergency_contact ?? ""}
                                disabled
                            />
                        <InputField
                                label="Histórico Médico"
                                value={user.medical_history ?? ""}
                                disabled
                                isTextArea={true}
                        />
                    </div>
                )}

                </>
            ) : (
                <p>Nenhum usuário selecionado.</p>
            )}

            <div className="w-full flex justify-center bg-white p-3 rounded-b-2xl">
                <DialogFooter>
                    <DialogClose className="text-white text-base bg-foreground px-5 py-1 rounded hover:scale-102 hover:bg-error transition cursor-pointer">
                        Fechar
                    </DialogClose>
                </DialogFooter>
            </div>

            </DialogDescription>
        </DialogHeader>
        </DialogContent>
    );
}

function ModalEdit({ user, type }: ModalProps) {
    if (!user) return null;

    const [isSaving, setIsSaving] = useState(false);
    const [errorMessage, setErrorMessage] = useState("");
    const fileInputRef = useRef<HTMLInputElement>(null);
    const getInitials = useInitials();

    const {
        preview,
        gender,
        formData,
        handleChange,
        handleImageChange,
        handleSelectChange,
        initializeEditMode
    } = useModal();

    useEffect(() => {
        initializeEditMode(user);
        setErrorMessage("");
    }, [user]);

    const handleSave = () => {
        if (!user || isSaving) return;

        setErrorMessage("");

        const validationErrors = validateUserData(formData, type, true, fileInputRef.current);
        
        if (validationErrors.length > 0) {
            setErrorMessage(validationErrors[0]);
            return;
        }

        setIsSaving(true);

        const submitData: Record<string, any> = {
            name: formData.name,
            email: formData.email,
            phone: formData.phone,
            _method: 'PUT',
        };

        if (type === 'patient') {
            if (formData.medical_history !== undefined) {
            submitData.medical_history = formData.medical_history;
            }
            if (formData.emergency_contact !== undefined) {
            submitData.emergency_contact = formData.emergency_contact;
            }
            if (formData.gender !== undefined) {
            submitData.gender = formData.gender;
            }
        }

        if (type === 'admin' && formData.is_master !== undefined) {
            submitData.is_master = formData.is_master;
        }

        if (type === 'doctor' && formData.crm !== undefined) {
            submitData.crm = formData.crm;
        }

        if (type === 'receptionist' && formData.register_number !== undefined) {
            submitData.register_number = formData.register_number;
        }

        if (fileInputRef.current?.files?.[0]) {
            submitData.photo = fileInputRef.current.files[0];
        }

        let updateUrl = '';
        switch (type) {
            case 'admin':
                updateUrl = `/admin/admins/${user.id}`;
                break;
            case 'doctor':
                updateUrl = `/admin/doctors/${user.id}`;
                break;
            case 'receptionist':
                updateUrl = `/admin/receptionists/${user.id}`;
                break;
            case 'patient':
                updateUrl = `/receptionist/patients/${user.id}`;
                break;
            default:
                setErrorMessage('Tipo de usuário inválido');
                setIsSaving(false);
                return;
        }

        router.post(updateUrl, submitData, {
            onSuccess: () => {
                window.location.reload();
            },
            onError: (errors) => {
                console.error('Erro:', errors);
                
                if (errors.message) {
                    setErrorMessage(errors.message);
                } else if (Object.keys(errors).length > 0) {
                    const firstError = Object.values(errors)[0];
                    setErrorMessage(Array.isArray(firstError) ? firstError[0] : firstError);
                } else {
                    setErrorMessage('Erro ao atualizar usuário. Tente novamente.');
                }
            },
            onFinish: () => {
                setIsSaving(false);
            }
        });
    };


    return (
        <DialogContent className="bg-foreground p-0 pt-3 rounded-2xl">
        <DialogHeader>
            <DialogTitle className="text-white text-center p-2">Editar {user ? user.name : type === "admin" ? "Administrador" : type === "receptionist" ? "Recepcionista" : type === "doctor" ? "Doutor" : "Paciente"}</DialogTitle>
        </DialogHeader>
            
            <div className="flex-col max-h-[86vh] bg-white p-4 rounded-b-2xl space-y-4 text-[#030D29] overflow-y-auto flex-1 custom-scrollbar">
            {errorMessage && (
                <InputError message={errorMessage} className="bg-red-50 border border-red-200 rounded-lg p-3 mb-4" />
            )}
            {type !== "patient" && (
                <div className="flex flex-col items-center gap-2">
                    <Avatar className="h-24 w-24">
                        <AvatarImage 
                            src={preview} 
                            alt={user.name} 
                            className="object-cover w-full h-full rounded-full"
                        />
                        <AvatarFallback className="text-2xl">
                            {getInitials(user.name)}
                        </AvatarFallback>
                    </Avatar>
                    <label className="bg-gray-200 p-1 rounded cursor-pointer text-sm">
                        Editar Foto
                        <input 
                            ref={fileInputRef}
                            type="file" 
                            accept="image/*" 
                            onChange={handleImageChange} 
                            className="hidden" 
                        />
                    </label>
                </div>
            )}

            <InputField label="Nome" icon={<FontAwesomeIcon icon={faUser} />} name="name" value={formData.name} onChange={handleChange} />
            <InputField label="E-mail" icon={<FontAwesomeIcon icon={faEnvelope} />} name="email" value={formData.email} onChange={handleChange} />
            <InputField label="Telefone" icon={<FontAwesomeIcon icon={faPhone} />} name="phone" value={formData.phone} onChange={handleChange} />

            { type=="patient" && (
                <div className="flex flex-col gap-3">   
                    <InputField
                        label="Contato de Emergência"
                        icon={<FontAwesomeIcon icon={faCommentMedical} />}
                        name="emergency_contact"
                        value={formData.emergency_contact}
                        onChange={handleChange}
                    />
                    <SelectField
                        label="Gênero"
                        name="gender"
                        value={formData.gender}
                        onChange={handleSelectChange}
                        options={[
                        { label: "Feminino", value: "female" },
                        { label: "Masculino", value: "male" },
                        { label: "Outro", value: "other" },
                        ]}
                    />
                    <InputField
                        label="Histórico Médico"
                        name="medical_history"
                        value={formData.medical_history ?? ""}
                        onChange={handleChange}
                        isTextArea
                    />
            </div>
            )}

            <div className="w-full flex justify-center pt-4 gap-3">
            <button 
                    onClick={(type === "admin" || type === "doctor" || type === "receptionist" || type === "patient") ? handleSave : undefined}
                    disabled={(type === "admin" || type === "doctor" || type === "receptionist" || type === "patient") ? isSaving : false}
                    className={`text-white text-base px-5 py-1 rounded hover:scale-105 transition cursor-pointer ${
                        (type === "admin" || type === "doctor" || type === "receptionist" || type === "patient") && isSaving 
                            ? 'bg-gray-400 cursor-not-allowed' 
                            : 'bg-foreground hover:bg-success'
                    }`}
                >
                    {(type === "admin" || type === "doctor" || type === "receptionist" || type === "patient") && isSaving ? 'Salvando...' : 'Salvar'}
                </button>
                <DialogClose className="bg-foreground text-white text-base px-5 py-1 rounded hover:scale-105 hover:bg-error transition cursor-pointer">Fechar</DialogClose>
            </div>
            </div>
        </DialogContent>
    );
}

function ModalCreate ({user, type}: ModalProps){
    const [isCreating, setIsCreating] = useState(false);
    const [errorMessage, setErrorMessage] = useState("");
    const fileInputRef = useRef<HTMLInputElement>(null);
    
    const {
        preview,
        gender,
        is_master,
        formData,
        handleChange,
        handleImageChange,
        handleSelectChange,
        resetFormData,
    } = useModal();
    
    useEffect(() => {
        resetFormData();
        setErrorMessage("");
    }, []);

    const handleCreate = () => {
        if (isCreating) return;
        
        setErrorMessage("");

        const validationErrors = validateUserData(formData, type, false, fileInputRef.current);
        
        if (validationErrors.length > 0) {
            setErrorMessage(validationErrors[0]);
            return;
        }

        setIsCreating(true);

        const submitData: Record<string, any> = {
            name: formData.name,
            email: formData.email,
            cpf: formData.cpf,
            phone: formData.phone,
            birth_date: formData.birth_date,
        };

        if (type !== "patient") {
            submitData.password = formData.password;
        }

        if (type === "admin") {
            submitData.is_master = formData.is_master;
        }

        if (type === "doctor") {
            submitData.crm = formData.crm;
        }

        if (type === "receptionist") {
            submitData.register_number = formData.register_number;
        }

        if (type === "patient") {
            submitData.gender = formData.gender;
            submitData.emergency_contact = formData.emergency_contact;
            submitData.medical_history = formData.medical_history;
        }

        if (fileInputRef.current?.files?.[0]) {
            submitData.photo = fileInputRef.current.files[0];
        }

        let createUrl = '';
        switch (type) {
            case 'admin':
                createUrl = '/admin/admins';
                break;
            case 'doctor':
                createUrl = '/admin/doctors';
                break;
            case 'receptionist':
                createUrl = '/admin/receptionists';
                break;
            case 'patient':
                createUrl = '/receptionist/patients';
                break;
            default:
                setErrorMessage('Tipo de usuário inválido');
                setIsCreating(false);
                return;
        }

        router.post(createUrl, submitData, {
            onSuccess: () => {
                window.location.reload();
            },
            onError: (errors) => {
                console.error('Erro:', errors);
                
                if (errors.message) {
                    setErrorMessage(errors.message);
                } else if (Object.keys(errors).length > 0) {
                    const firstError = Object.values(errors)[0];
                    setErrorMessage(Array.isArray(firstError) ? firstError[0] : firstError);
                } else {
                    setErrorMessage('Erro ao criar usuário. Tente novamente.');
                }
            },
            onFinish: () => {
                setIsCreating(false);
            }
        });
    };

    return (
        <DialogContent className="bg-foreground p-0 pt-3 rounded-2xl ">
            <DialogHeader className="flex-shrink-0">
                <DialogTitle className="text-white text-center p-2">Criar {type === "admin" ? "Administrador" : type === "receptionist" ? "Recepcionista" : type === "doctor" ? "Doutor" : "Paciente"}</DialogTitle>
                <DialogDescription className="max-h-[86vh] bg-white-50 p-4 rounded-b-2xl space-y-4 text-digital-blue-800 overflow-y-auto flex-1 custom-scrollbar flex-col">
                    {type !== "patient" && (
                        <div className="flex flex-col items-center gap-2">
                            <Avatar className="h-24 w-24">
                                <AvatarImage
                                    src={preview}
                                    alt="Preview"
                                className="object-cover w-full h-full rounded-full"
                            />
                            <AvatarFallback className="text-gray-700 flex items-center justify-center">
                                <FontAwesomeIcon icon={faUser} className="text-3xl" />
                            </AvatarFallback>
                        </Avatar>
                        <label className="bg-gray-200 rounded cursor-pointer text-sm font-semibold p-1 hover:bg-gray-300">
                            Adicionar Foto
                            <input 
                                ref={fileInputRef}
                                type="file" 
                                accept="image/*" 
                                onChange={handleImageChange} 
                                className="hidden" 
                            />
                        </label>
                        </div>
                    )}
                    <InputField name="name" label="Nome" icon={<FontAwesomeIcon icon={faUser} />} value={formData.name} placeholder="Digite o nome" onChange={handleChange} />
                    <InputField name="email" label="E-mail" icon={<FontAwesomeIcon icon={faEnvelope} />} value={formData.email} placeholder="Digite o e-mail" onChange={handleChange} />
                    {type !== "patient" && (
                        <InputField name="password" label="Senha" type="password" icon={<FontAwesomeIcon icon={faKey} />} value={formData.password} placeholder="Digite a senha" onChange={handleChange} />
                    )}
                    <div className="flex gap-3">
                        <InputField name="phone" label="Telefone" icon={<FontAwesomeIcon icon={faPhone} />} value={formData.phone} 
                        placeholder="Digite o telefone" onChange={handleChange} />
                        <InputField name="cpf" label="CPF" icon={<FontAwesomeIcon icon={faIdCard} />} value={formData.cpf} placeholder="Digite o CPF" onChange={handleChange} />
                    </div>
                    <InputField name="birth_date" label="Data de Nascimento" icon={<FontAwesomeIcon icon={faCalendar} />} value={formData.birth_date} type="date" onChange={handleChange} />

                    {type === "patient" && (
                        <div className="flex flex-col gap-3">
                            <SelectField
                                name="gender"
                                label="Gênero"
                                options={[
                                    { label: "Feminino", value: "female" },
                                    { label: "Masculino", value: "male" },
                                    { label: "Outro", value: "other" },
                                ]}
                                onChange={handleSelectChange}
                                value={gender}
                            />
                            <InputField name="emergency_contact" label="Contato de Emergência" icon={<FontAwesomeIcon icon={faCommentMedical} />} value={formData.emergency_contact} placeholder="Digite o contato de emergência" onChange={handleChange} />
                            <InputField name="medical_history" label="Histórico Médico" value={formData.medical_history} isTextArea={true} placeholder="Digite o histórico médico" onChange={handleChange} />
                        </div>
                    )}

                    {type === "doctor" && (
                        <div className="flex gap-3">
                            <InputField name="crm" label="CRM" icon={<FontAwesomeIcon icon={faIdCardClip} />} value={formData.crm} placeholder="Digite o CRM" onChange={handleChange} />
                        </div>
                    )}

                    {type === "receptionist" && (
                        <InputField name="register_number" label="Número de Registro" icon={<FontAwesomeIcon icon={faIdCardClip} />} value={formData.register_number} placeholder="Digite o número de registro" onChange={handleChange} />
                    )}

                    {type === "admin" && (
                        <SelectField
                            name="is_master"
                            label="Administrador Master"
                            options={[
                                { label: "Selecione a opção", value: "" },
                                { label: "Sim", value: "yes" },
                                { label: "Não", value: "no" },
                            ]}
                            onChange={handleSelectChange}
                            value={is_master}
                        />
                    )}
                    <InputError message={errorMessage} className="bg-red-50 border border-red-200 rounded-lg p-3 mb-4" />

                    <div className="w-full flex justify-center pt-4 gap-3">
                        <button 
                            onClick={(type === "admin" || type === "doctor" || type === "receptionist" || type === "patient") ? handleCreate : undefined}
                            disabled={(type === "admin" || type === "doctor" || type === "receptionist" || type === "patient") ? isCreating : false}
                            className={`text-white text-base px-5 py-1 rounded hover:scale-105 transition cursor-pointer ${
                                (type === "admin" || type === "doctor" || type === "receptionist" || type === "patient") && isCreating 
                                    ? 'bg-gray-400 cursor-not-allowed' 
                                    : 'bg-foreground hover:bg-success'
                            }`}
                        > 
                            {(type === "admin" || type === "doctor" || type === "receptionist" || type === "patient") && isCreating ? "Criando..." : "Criar"}
                        </button>
                        <DialogClose className="bg-foreground text-white px-5 py-1 rounded hover:scale-105 hover:bg-error transition cursor-pointer text-base">Fechar</DialogClose>
                    </div>
                </DialogDescription>
            </DialogHeader>
        </DialogContent>
    );
}

function ModalDelete({ user, type }: ModalProps) {
    if (!user) return null;

    const [isDeleting, setIsDeleting] = useState(false);

    const handleDelete = () => {
        if (!user || isDeleting) return;

        setIsDeleting(true);

        let deleteUrl = '';
        switch (type) {
            case 'admin':
                deleteUrl = `/admin/admins/${user.id}`;
                break;
            case 'doctor':
                deleteUrl = `/admin/doctors/${user.id}`;
                break;
            case 'receptionist':
                deleteUrl = `/admin/receptionists/${user.id}`;
                break;
            case 'patient':
                deleteUrl = `/receptionist/patients/${user.id}`;
                break;
            default:
                alert('Tipo de usuário inválido');
                setIsDeleting(false);
                return;
        }

        router.delete(deleteUrl, {
            onSuccess: () => {
                window.location.reload();
            },
            onError: (errors) => {
                console.error('Erro:', errors);
                
                if (errors.message) {
                    alert(errors.message);
                } else if (Object.keys(errors).length > 0) {
                    const firstError = Object.values(errors)[0];
                    alert(Array.isArray(firstError) ? firstError[0] : firstError);
                } else {
                    alert('Erro ao deletar usuário');
                }
            },
            onFinish: () => {
                setIsDeleting(false);
            }
        });
    };

    return (
        <DialogContent className="bg-foreground p-0 pt-3 rounded-2xl overflow-y-auto">
            <DialogHeader>
                <DialogTitle className="text-white text-center p-2">Excluir {user ? user.name : type === "admin" ? "Administrador" : type === "receptionist" ? "Recepcionista" : type === "doctor" ? "Doutor" : "Paciente"}</DialogTitle>
                <DialogDescription className=" flex flex-col items-center text-base bg-white-50 text-darktext rounded-b-2xl space-y-4 p-7">
                    <div className="text-center">
                        <p className="mb-2">Tem certeza que deseja excluir o usuário <strong>{user.name}</strong>?</p>
                        <p className="text-sm text-gray-600">Esta ação não pode ser desfeita.</p>
                </div>
                <div className="w-full flex justify-center bg-white-50 p-3 rounded-b-2xl gap-3">
                    <button
                        onClick={handleDelete}
                        disabled={isDeleting}
                        className={`text-white text-base px-5 py-1 rounded hover:scale-105 transition cursor-pointer ${
                            isDeleting 
                                ? 'bg-gray-400 cursor-not-allowed' 
                                : 'bg-error hover:bg-error/90'
                        }`}
                    >
                        {isDeleting ? 'Excluindo...' : 'Excluir'}
                    </button>
                    <DialogClose className="text-white text-base bg-foreground px-5 py-1 rounded hover:scale-105 transition cursor-pointer">
                        Cancelar
                    </DialogClose>
                </div>
            </DialogDescription>
        </DialogHeader>
        </DialogContent>
    );
}

function ModalAppointment({ receptionist, patients, doctors }: { receptionist: User | null, patients: User[], doctors: User[] }) {
    const {
        appointmentFormData,
        searchQuery,
        filteredPatients,
        selectedPatient,
        doctorQuery,
        filteredDoctors,
        selectedDoctor,
        setSearchQuery,
        setFilteredPatients,
        setSelectedPatient,
        setDoctorQuery,
        setFilteredDoctors,
        setSelectedDoctor,
        handleAppointmentChange,
        handlePatientSelect,
        handleDoctorSelect,
        handleCreateAppointment,
        resetAppointmentData,
        searchPatients,
        searchDoctors,
    } = useModal();

    const formatPrice = (price: number): string => {
        return price.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    const formatPriceForInput = (price: number): string => {
        if (price === 0) return '';
        return price.toLocaleString('pt-BR');
    };

    useEffect(() => {
        resetAppointmentData();
    }, []);

    if (!receptionist) return null;

    return (
        <DialogContent className="bg-foreground p-0 pt-3 rounded-2xl">
            <DialogHeader className="flex-shrink-0">
                <DialogTitle className="text-white text-center p-2">Agendar Consulta</DialogTitle>
                <DialogDescription className="max-h-[86vh] bg-white-50 p-4 rounded-b-2xl space-y-4 text-darktext overflow-y-auto flex-1 custom-scrollbar flex-col">
                    <div className="flex flex-col gap-3">
                        <div className="relative">
                            <InputField
                                name="patient"
                                label="Paciente"
                                value={searchQuery}
                                onChange={e => {
                                    const value = e.target.value;
                                    setSearchQuery(value);
                                    setSelectedPatient(null);
                                    searchPatients(value);
                                }}
                                placeholder="Busque pelo nome do paciente"
                            />
                            {filteredPatients.length > 0 && (
                                <ul className="absolute z-10 bg-white border rounded w-full mt-1 max-h-40 overflow-y-auto shadow">
                                    {filteredPatients.map(p => (
                                        <li
                                            key={p.id}
                                            className="px-3 py-2 cursor-pointer hover:bg-white-300"
                                            onClick={() => handlePatientSelect(p)}
                                        >
                                            {p.name}
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </div>
                        <div className="relative">
                            <InputField
                                name="doctor"
                                label="Doutor"
                                value={doctorQuery}
                                onChange={e => {
                                    const value = e.target.value;
                                    setDoctorQuery(value);
                                    setSelectedDoctor(null);
                                    searchDoctors(value);
                                }}
                                placeholder="Busque pelo nome do doutor"
                            />
                            {filteredDoctors.length > 0 && (
                                <ul className="absolute z-10 bg-white-50 border rounded w-full mt-1 max-h-40 overflow-y-auto shadow">
                                    {filteredDoctors.map(d => (
                                        <li
                                            key={d.id}
                                            className="px-3 py-2 cursor-pointer hover:bg-white-300"
                                            onClick={() => handleDoctorSelect(d)}
                                        >
                                            {d.name}
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </div>
                        <div className="flex gap-3">
                            <InputField name="date" label="Data" type="date" value={appointmentFormData.date} onChange={handleAppointmentChange} />
                            <InputField name="time" label="Hora" type="time" value={appointmentFormData.time} onChange={handleAppointmentChange} />
                        </div>
                        <div className="flex flex-col">
                            <InputField
                                name="price"
                                label="Preço da Consulta"
                                type="text"
                                value={formatPriceForInput(appointmentFormData.price)}
                                onChange={handleAppointmentChange}
                                placeholder="0,00"
                            />
                            {appointmentFormData.price > 0 && (
                                <div className="text-sm text-gray-600 mt-1">
                                    Valor: {formatPrice(appointmentFormData.price)}
                                </div>
                            )}
                        </div>
                    </div>
                    <div className="w-full flex justify-center pt-4 gap-3">
                        <button
                            className="bg-foreground text-white text-base px-5 py-1 rounded hover:scale-105 transition cursor-pointer hover:bg-success"
                            onClick={handleCreateAppointment}
                            disabled={!selectedDoctor}
                        >
                            Agendar
                        </button>
                        <DialogClose className="bg-foreground text-white px-5 py-1 rounded hover:scale-105 hover:bg-error transition cursor-pointer text-base">Fechar</DialogClose>
                    </div>
                </DialogDescription>
            </DialogHeader>
        </DialogContent>
    );
}

export{
    ModalView,
    ModalEdit,
    ModalCreate,
    ModalDelete,
    ModalProvider,
    useModal,
    ModalAppointment
}